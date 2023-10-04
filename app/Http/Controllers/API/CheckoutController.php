<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use Midtrans\Snap;
use App\Models\Cart;
use App\Models\User;
use Midtrans\Config;
use App\Models\Order;
use App\Models\Produk;
use App\Models\SaldoToko;
use App\Models\DetailToko;
use Midtrans\Notification;
use App\Models\DetailOrder;
use Illuminate\Support\Str;
use App\Models\SaldoRefaund;
use Illuminate\Http\Request;
use App\Jobs\SendInvoiceToko;
use App\Models\AksesDownload;
use App\Models\ClearingSaldo;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\Handle\ErrorController;
use App\Http\Controllers\API\Payment\SaldoTokoController;
use App\Models\IorPay;
use App\Models\TrxIorPay;
use Error;

class CheckoutController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = 'SB-Mid-server-vQYtgExR76-NLFZqSYo-C5MX';
        Config::$clientKey = 'SB-Mid-client-TKuIPz4RRzS3pxRC';
        Config::$isProduction = false;
        Config::$is3ds = false;
    }
    
    public function checkout(Request $request) {
        
        try {
            $items = array();
            $total = 0;
            $total_potongan = 0;
            $potongan_referal = 0;
            // Hitung Total Biaya Dan Potongan
            foreach($request['carts'] as  $item) {
                $produk = Produk::where('kode_produk', $item['kode_produk'])->first();
                $potongan = 0;
                if($produk->potongan_harga > 0) {
                    $potongan = (float) ($produk->harga - $produk->potongan_harga);
                    $total_potongan += $produk->potongan_harga;
                }
        
                if($produk->potongan_persen > 0) {
                    $potongan = (float) $produk->harga * ($produk->potongan_persen  / 100);
                    $total_potongan += $potongan;
                    $potongan = (float) ($produk->harga - $potongan);
                }

                $total += (float) ($potongan > 0 ? $potongan : $produk->harga);

            }

            $biaya_platform = 10;
            $kode_unique = rand(111, 999);
            $order = new Order();
            $order->biaya_platform = $biaya_platform;
            $order->no_order = 'TRX'.rand();
            $order->uuid_user = Auth::user()->uuid;
            $order->quantity = count($request['carts']);
            $order->type_payment = $request['type_payment'];

            $biaya_platform = (float) $biaya_platform / 100;
            $biaya_platform = (float) $total * $biaya_platform;
            
            $order->payment_method = $request['method'];
            if($request['type_payment'] == 'manual') {
                $order->total_biaya = (float) $total + $kode_unique;
                $kode_unique = substr($order->total_biaya, -3);
                $order->kode_unique = $kode_unique;
            }else {
                $order->total_biaya = $total;
            }

            if($request['type_payment'] == 'iorpay') {
                $pay = IorPay::where('uuid_user', Auth::user()->uuid)->first();

                if(isset($pay)) {
                    if(intval($pay->saldo) <= 0) {
                        return response()->json([
                            'status' => true,
                            'error' => true,
                            'message' => 'Saldo Anda Rp. '.number_format($pay->saldo, 0).' Dan Tidak Mencukupi Untuk Melakukan Pembelian Produk Ini, Silahkan Melakukan Topup Terlebih Dahulu.',
                            'redirect' => true
                        ], 200);
                    }

                    if(intval($pay->saldo) < $order->total_biaya) {
                        return response()->json([
                            'status' => true,
                            'error' => true,
                            'message' => 'Saldo Anda Rp. '.number_format($pay->saldo, 0).' Dan Tidak Mencukupi Untuk Melakukan Pembelian Produk Ini, Silahkan Melakukan Topup Terlebih Dahulu.',
                            'redirect' => true
                        ], 200);
                    }

                    $current_saldo = intval($pay->saldo);
                    $pay->saldo = (float) $current_saldo - $order->total_biaya;
                    
                    if($pay->save()) {

                        $trx_pay = new TrxIorPay();
                        $trx_pay->no_trx = 'TRX-'.rand(100000000, 999999999);
                        $trx_pay->kode_pay = $pay->kode_pay;
                        $trx_pay->uuid_user = Auth::user()->uuid;
                        $trx_pay->type_pay = 'CREDIT';
                        $trx_pay->jenis_pembayaran = $request['type_payment'];
                        $trx_pay->total_trx = $order->total_biaya;
                        $trx_pay->total_fixed = $order->total_biaya;
                        $trx_pay->keterangan = 'Pembelian Produk';
                        $trx_pay->status_trx = 'SUCCESS';
                        $trx_pay->save();

                        $order->status_order = 'SUCCESS';

                        foreach($request['carts'] as $item) {
                            $produk = Produk::where('kode_produk', $item['kode_produk'])->first();
                            
                            if($produk->type_produk == 'AUTO') {
                                $token = Str::uuid(32);
                                
                                $check_akses = AksesDownload::where([
                                                    'kode_produk' => $produk->kode_produk,
                                                    'uuid_user' => $order->uuid_user
                                                ])->first();
    
                                if(empty($check_akses)) {
                                    AksesDownload::create([
                                        'kode_produk' => $produk->kode_produk,
                                        'uuid_user' => $order->uuid_user,
                                        'token' => $token,
                                        'no_order' => $order->no_order,
                                        'url_file' => 'storage/file_produk/'.$produk->toko->kode_toko.'/'.$produk->file_name
                                    ]);
                                }
                            }

                            $order_toko = DetailOrder::where([
                                'no_order' => $order->no_order,
                                'kode_toko' => $produk->kode_toko
                            ])->get();
           
                            SaldoRefaund::addSaldo($produk->kode_toko, $total);

                            ClearingSaldo::create([
                                'kode_toko' => $produk->kode_toko,
                                'saldo' => $total,
                                'tanggal_insert' => now()->format('Y-m-d'),
                                'jadwal_clear' => Carbon::now()->addDay(3)->format('Y-m-d')
                            ]);
                        }
                    }
                }
            }

            $this->transaksiReferal($request);
            $order->total_potongan = (float) $total_potongan;
            
            foreach($request['carts'] as  $item) {
                $produk = Produk::where('kode_produk', $item['kode_produk'])->first();
                $potongan = 0;
                if($produk->potongan_harga > 0) {
                    $potongan = (float) ($produk->harga - $produk->potongan_harga);
                    $total_potongan += $produk->potongan_harga;
                }
        
                if($produk->potongan_persen > 0) {
                    $potongan = (float) $produk->harga * ($produk->potongan_persen  / 100);
                    $total_potongan += $potongan;
                    $potongan = (float) ($produk->harga - $potongan);
                }

                $cart = Cart::where([
                                'kode_produk' => $item['kode_produk'],
                                'uuid_user' => Auth::user()->uuid
                            ])->first();

                $produk = Produk::where('kode_produk', $item['kode_produk'])->first();
                $detail = new DetailOrder();
                $detail->no_order = $order->no_order;
                $detail->kode_produk = $item['kode_produk'];
                $detail->uuid_user = Auth::user()->uuid;
                $detail->kode_toko = $produk->toko->kode_toko;
                $detail->kode_referal = $cart->referal;
                $detail->quantity = 1;
                $detail->save();

                array_push($items, [
                    'id' => $detail->kode_produk,
                    'price' => ($potongan > 0 ? $potongan : $produk->harga),
                    'quantity' => $detail->quantity,
                    'name' => $item['nm_produk']
                ]);
                
                Cart::where([
                    'kode_produk' => $item['kode_produk'],
                    'uuid_user' => Auth::user()->uuid
                ])->delete();

            }
        
            $params = array(
                'transaction_details' => array(
                    'order_id' => $order->no_order,
                    'gross_amount' => $order->total_biaya
                )
            );

            $billing_address = array(
                'first_name'   => Auth::user()->full_name,
                'address'      => Auth::user()->alamat,
                'city'         => Auth::user()->alamat,
                'phone'        => Auth::user()->no_hape,
                'country_code' => 'IDN'
            );

            $params['customer_details'] = $billing_address;
            $params['item_details'] = $items;

            if($request['type_payment'] == 'gateway') {
                $snapToken = Snap::getSnapToken($params);
            }

            $order->snap_token = ($request['type_payment'] == 'gateway' ? $snapToken : NULL);
            $order->save();
            DB::commit();
            return response()->json([
                'status' => true,
                'detail_token' => ($request['type_payment'] == 'gateway' ? $snapToken : $order->no_order),
                'detail_customer' => $params,
                'message' => 'Selamat, Transaksi Anda Berhasil.'
            ], 200);
        }catch(\Exception $err) {
            DB::rollBack();
            return ErrorController::getResponseError($err);
        }
    }

    public function transaksiReferal(Request $request) {
        try {

            foreach($request['carts'] as $item) {
                $produk = Produk::where('kode_produk', $item['kode_produk'])->first();
                if($request['type_payment'] == 'manual' || $request['type_payment'] == 'gateway') {
    
                    $cart = Cart::where([
                        'kode_produk' => $item['kode_produk'],
                        'uuid_user' => Auth::user()->uuid
                        ])->first();
                    
                    if($cart->referal) {
                        // ambil iorpay yang menshare link
                        if($produk->status_referal) {
                            $pay_referal = IorPay::where('uuid_user', $cart->referal)->first();

                            $trx_pay = new TrxIorPay();
                            $trx_pay->no_trx = 'TRX-'.rand(100000000, 999999999);
                            $trx_pay->kode_pay = $pay_referal->kode_pay;
                            $trx_pay->uuid_user = $pay_referal->user->uuid;
                            $trx_pay->type_pay = 'DEBIT';
                            $trx_pay->jenis_pembayaran = 'REFERAL';
                            $trx_pay->total_trx = $produk->komisi_referal;
                            $trx_pay->total_fixed = $produk->komisi_referal;
                            $trx_pay->keterangan = 'Komisi Referal';
                            $trx_pay->status_trx = 'PENDING';
                            $trx_pay->save();
                        }
                    }
                }else {
                    $cart = Cart::where([
                        'kode_produk' => $item['kode_produk'],
                        'uuid_user' => Auth::user()->uuid
                        ])->first();
                    
                    if($cart->referal) {
                        // ambil iorpay yang menshare link
                        if($produk->status_referal) {
                            $pay_referal = IorPay::where('uuid_user', $cart->referal)->first();
                            $pay_referal->saldo += $produk->komisi_referal;
                            $pay_referal->save();

                            $trx_pay = new TrxIorPay();
                            $trx_pay->no_trx = 'TRX-'.rand(100000000, 999999999);
                            $trx_pay->kode_pay = $pay_referal->kode_pay;
                            $trx_pay->uuid_user = $pay_referal->user->uuid;
                            $trx_pay->type_pay = 'DEBIT';
                            $trx_pay->jenis_pembayaran = 'REFERAL';
                            $trx_pay->total_trx = $produk->komisi_referal;
                            $trx_pay->total_fixed = $produk->komisi_referal;
                            $trx_pay->keterangan = 'Komisi Referal';
                            $trx_pay->status_trx = 'SUCCESS';
                            $trx_pay->save();

                        }
                    }
                }
            }
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function notification() {
            $notif = new Notification();
            $transaction = $notif->transaction_status;
            $paymentType = $notif->payment_type;
            $fraud = $notif->fraud_status;
            $orderId = $notif->order_id;
            $order = Order::where('no_order', $orderId)->first();
            error_log("Order ID $notif->order_id: "."transaction status = $transaction, fraud staus = $fraud");
            if ($transaction == 'capture') {
                if ($fraud == 'challenge') {
                    $order->status_order = 'PENDING';
                }
                else if ($fraud == 'accept') {
                    $order->status_order = 'SUCCESS';
                    $daftar_order_toko = array();
                    foreach($order->detail as $detail) {
                        $produk = Produk::where('kode_produk', $detail->kode_produk)->first();
                        $harga = $produk->getHargaDiskon($produk);
                        $harga_fixed = (float)str_replace(',', '', $harga['harga_fixed']);
                        $biaya_platform = (float) (10 / 100);
                        $potongan_platform = (float) ($harga_fixed * $biaya_platform);
                        $pendapatan_toko = (float) ($harga_fixed - $potongan_platform);

                        
                        if($detail->kode_referal) {
                            // ambil iorpay yang menshare link
                            if($produk->status_referal) {
                                $pay_referal = IorPay::where('uuid_user', $detail->kode_referal)->first();
                                $pay_referal->saldo += $produk->komisi_referal;
                                $pay_referal->save();

                                $trx_pay = new TrxIorPay();
                                $trx_pay->no_trx = 'TRX-'.rand(100000000, 999999999);
                                $trx_pay->kode_pay = $pay_referal->kode_pay;
                                $trx_pay->uuid_user = $pay_referal->user->uuid;
                                $trx_pay->type_pay = 'DEBIT';
                                $trx_pay->jenis_pembayaran = 'REFERAL';
                                $trx_pay->total_trx = $produk->komisi_referal;
                                $trx_pay->total_fixed = $produk->komisi_referal;
                                $trx_pay->keterangan = 'Komisi Referal';
                                $trx_pay->status_trx = 'SUCCESS';
                                $trx_pay->save();
                            }
                        }
                        if($produk->type_produk == 'AUTO') {
                            $token = Str::uuid(32);
                            
                            $check_akses = AksesDownload::where([
                                                'kode_produk' => $produk->kode_produk,
                                                'uuid_user' => $order->uuid_user
                                            ])->first();

                            if(empty($check_akses)) {
                                AksesDownload::create([
                                    'kode_produk' => $produk->kode_produk,
                                    'uuid_user' => $detail->uuid_user,
                                    'token' => $token,
                                    'no_order' => $detail->no_order,
                                    'url_file' => 'storage/file_produk/'.$produk->toko->kode_toko.'/'.$produk->file_name
                                ]);
                            }
                        }
                        $order_toko = DetailOrder::where([
                                            'no_order' => $order->no_order,
                                            'kode_toko' => $produk->kode_toko
                                        ])->get();
                        array_push($daftar_order_toko, $produk->kode_toko);
                        error_log("Pendapatan Tok : $pendapatan_toko");
                        SaldoRefaund::addSaldo($produk->kode_toko, $pendapatan_toko);
                        ClearingSaldo::create([
                            'kode_toko' => $produk->kode_toko,
                            'saldo' => $pendapatan_toko,
                            'tanggal_insert' => now()->format('Y-m-d'),
                            'jadwal_clear' => Carbon::now()->addDay(3)->format('Y-m-d')
                        ]);
                        
                    }

                    $group_toko = array_unique($daftar_order_toko);

                    foreach($group_toko as $toko) {
                        $daftar_toko = DetailToko::where('kode_toko', $toko)->first();
                        $get_order_toko = DetailOrder::where([
                            'no_order' => $order->no_order,
                            'kode_toko' => $toko
                        ])->get();

                        $total_pembayaran_pertoko = 0;
                        foreach($get_order_toko as $ot) {
                            $produk_toko = Produk::where('kode_produk', $ot->kode_produk)->first();
                            $harga_produk = $produk_toko->getHargaDiskon($produk_toko);
                            $harga_fixed = (float)str_replace(',', '', $harga_produk['harga_fixed']);
                            $total_pembayaran_pertoko += $harga_fixed;
                        }

                        $biaya_platform = (float) (10 / 100);
                        $potongan_platform = (float) ($total_pembayaran_pertoko * $biaya_platform);
                        $pendapatan_toko = (float) ($total_pembayaran_pertoko - $potongan_platform);

                        $get_order_toko->total_biaya = $pendapatan_toko;
                        $user_toko = User::where('uuid', $daftar_toko->user->uuid)->first();
                        SendInvoiceToko::dispatch($user_toko, $get_order_toko);
                    }

                }
            }
            else if ($transaction == 'cancel') {
                if ($fraud == 'challenge') {
                    $order->status_order = 'PENDING';
                }
                else if ($fraud == 'accept') {
                // TODO Set payment status in merchant's database to 'failure'
                    $order->status_order = 'CANCEL';
                }
            }
            else if ($transaction == 'pending') {
                if ($fraud == 'challenge') {
                    $order->status_order = 'PENDING';
                }
                else if ($fraud == 'accept') {
                // TODO Set payment status in merchant's database to 'failure'
                    $order->status_order = 'PENDING';
                }
            }
            else if ($transaction == 'settlement') {
                if ($fraud == 'challenge') {
                    $order->status_order = 'PENDING';
                }else if ($fraud == 'accept') {
                // TODO Set payment status in merchant's database to 'failure'
                    $order->status_order = 'SUCCESS';
                    $daftar_order_toko = array();
                    foreach($order->detail as $detail) {
                        $produk = Produk::where('kode_produk', $detail->kode_produk)->first();
                        $harga = $produk->getHargaDiskon($produk);
                        $harga_fixed = (float)str_replace(',', '', $harga['harga_fixed']);
                        $biaya_platform = (float) (10 / 100);
                        $potongan_platform = (float) ($harga_fixed * $biaya_platform);
                        $pendapatan_toko = (float) ($harga_fixed - $potongan_platform);

                        if($detail->kode_referal) {
                            // ambil iorpay yang menshare link
                            if($produk->status_referal) {
                                $pay_referal = IorPay::where('uuid_user', $detail->kode_referal)->first();
                                $pay_referal->saldo += $produk->komisi_referal;
                                $pay_referal->save();

                                $trx_pay = new TrxIorPay();
                                $trx_pay->no_trx = 'TRX-'.rand(100000000, 999999999);
                                $trx_pay->kode_pay = $pay_referal->kode_pay;
                                $trx_pay->uuid_user = $pay_referal->user->uuid;
                                $trx_pay->type_pay = 'DEBIT';
                                $trx_pay->jenis_pembayaran = 'REFERAL';
                                $trx_pay->total_trx = $produk->komisi_referal;
                                $trx_pay->total_fixed = $produk->komisi_referal;
                                $trx_pay->keterangan = 'Komisi Referal';
                                $trx_pay->status_trx = 'SUCCESS';
                                $trx_pay->save();
                            }
                        }
                        if($produk->type_produk == 'AUTO') {
                            $token = Str::uuid(32);
                            
                            $check_akses = AksesDownload::where([
                                                'kode_produk' => $produk->kode_produk,
                                                'uuid_user' => $order->uuid_user
                                            ])->first();

                            if(empty($check_akses)) {
                                AksesDownload::create([
                                    'kode_produk' => $produk->kode_produk,
                                    'uuid_user' => $detail->uuid_user,
                                    'token' => $token,
                                    'no_order' => $detail->no_order,
                                    'url_file' => 'storage/file_produk/'.$produk->toko->kode_toko.'/'.$produk->file_name
                                ]);
                            }
                        }

                        $order_toko = DetailOrder::where([
                                            'no_order' => $order->no_order,
                                            'kode_toko' => $produk->kode_toko
                                        ])->get();

                        array_push($daftar_order_toko, $produk->kode_toko);
                        error_log("Pendapatan Tok : $pendapatan_toko");
                        SaldoRefaund::addSaldo($produk->kode_toko, $pendapatan_toko);
                        ClearingSaldo::create([
                            'kode_toko' => $produk->kode_toko,
                            'saldo' => $pendapatan_toko,
                            'tanggal_insert' => now()->format('Y-m-d'),
                            'jadwal_clear' => Carbon::now()->addDay(3)->format('Y-m-d')
                        ]);
                    }

                    $group_toko = array_unique($daftar_order_toko);

                    foreach($group_toko as $toko) {
                        $daftar_toko = DetailToko::where('kode_toko', $toko)->first();
                        $get_order_toko = DetailOrder::where([
                            'no_order' => $order->no_order,
                            'kode_toko' => $toko
                        ])->get();

                        $total_pembayaran_pertoko = 0;
                        foreach($get_order_toko as $ot) {
                            $produk_toko = Produk::where('kode_produk', $ot->kode_produk)->first();
                            $harga_produk = $produk_toko->getHargaDiskon($produk_toko);
                            $harga_fixed = (float)str_replace(',', '', $harga_produk['harga_fixed']);
                            $total_pembayaran_pertoko += $harga_fixed;
                        }

                        $biaya_platform = (float) (10 / 100);
                        $potongan_platform = (float) ($total_pembayaran_pertoko * $biaya_platform);
                        $pendapatan_toko = (float) ($total_pembayaran_pertoko - $potongan_platform);

                        $data['order'] = $get_order_toko;
                        $data['total_biaya'] = $pendapatan_toko;
                        $user_toko = User::where('uuid', $daftar_toko->user->uuid)->first();
                        SendInvoiceToko::dispatch($user_toko, $data);
                    }
                }
            }
            else if ($transaction == 'deny') {
                $order->status_order = 'CANCEL';
            }
            else if ($transaction == 'expire') {
                $order->status_order = 'EXPIRED';
            }
            $order->payment_method = $paymentType;
            $order->save();

            return;
    }
    public function transaction(Request $request) {
        try {
            $items = array();
            $total = 0;
            foreach($request['carts'] as  $item) {
                $produk = Produk::where('kode_produk', $item['kode_produk'])->first();
                $potongan = 0;
                if($produk->potongan_harga > 0) {
                    $potongan = (float) ($produk->harga - $produk->potongan_harga);
                }
        
                if($produk->potongan_persen > 0) {
                    $potongan = (float) $produk->harga * ($produk->potongan_persen  / 100);
                    $potongan = (float) ($produk->harga - $potongan);
                }

                array_push($items, [
                    'id' => $item['kode_produk'],
                    'price' => ($potongan > 0 ? $potongan : $produk->harga),
                    'quantity' => 1,
                    'name' => $item['nm_produk']
                ]);

                $total += ($potongan > 0 ? $potongan : $produk->harga);

            }
            $params = array(
                'transaction_details' => array(
                    'order_id' => 'TRX'.rand(),
                    'gross_amount' => $total,
                )
            );
            $billing_address = array(
                'first_name'   => Auth::user()->full_name,
                'address'      => Auth::user()->alamat,
                'city'         => Auth::user()->alamat,
                'phone'        => Auth::user()->no_hape,
                'country_code' => 'IDN'
            );

            $params['customer_details'] = $billing_address;
            $params['item_details'] = $items;
            
            $snapToken = Snap::getSnapToken($params);
        }catch(\Exception $err) {
            
        }
    }

    // public static function generateAksesDownload(Produk $produk) {
    //     $akses = 
    // }
}
