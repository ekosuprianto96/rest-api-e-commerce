<?php

namespace App\Http\Controllers\API;

use Error;
use Carbon\Carbon;
use Midtrans\Snap;
use App\Models\Cart;
use App\Models\User;
use Midtrans\Config;
use App\Models\Order;
use App\Models\IorPay;
use App\Models\MsMenu;
use App\Models\Produk;
use App\Models\SaldoToko;
use App\Models\TrxIorPay;
use App\Models\DetailToko;
use App\Models\Pendapatan;
use Midtrans\Notification;
use App\Models\DetailOrder;
use Illuminate\Support\Str;
use App\Models\SaldoRefaund;
use Illuminate\Http\Request;
use App\Jobs\SendInvoiceToko;
use App\Models\AksesDownload;
use App\Models\ClearingSaldo;
use App\Models\SettingGateway;
use App\Models\SettingWebsite;
use App\Models\TransaksiAccount;
use App\Events\NotificationAdmin;
use Illuminate\Support\Facades\DB;
use App\Events\NotifikasiOrderToko;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification as Notif;
use Illuminate\Support\Facades\Route;
use App\Models\TransaksiKomisiReferal;
use App\Http\Controllers\API\Handle\ErrorController;
use App\Http\Controllers\API\Payment\SaldoTokoController;
use App\Models\NotifikasiAdmin;

class CheckoutController extends Controller
{
    public $settings_gateway;
    public $settings_web;
    public function __construct()
    {
        $this->settings_gateway = SettingGateway::first();
        $this->settings_web = SettingWebsite::first();
        Config::$serverKey = $this->settings_gateway->server_key;
        Config::$clientKey = $this->settings_gateway->client_key;
        Config::$isProduction = $this->settings_gateway->is_production;
        Config::$is3ds = $this->settings_gateway->is_3ds;
    }
    
    public function checkout(Request $request) {
        
        try {
            
            DB::beginTransaction();
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
            
            $kode_unique = rand(111, 999);
            $order = new Order();
            $order->biaya_platform = 0;
            $order->no_order = 'TRX'.rand();
            $order->uuid_user = Auth::user()->uuid;
            $order->quantity = count($request['carts']);
            $order->type_payment = $request['type_payment'];
            
            $order->payment_method = $request['method'];
            if($request['type_payment'] == 'manual') {
                $order->total_biaya = (float) $total + $kode_unique;
                $kode_unique = substr($order->total_biaya, -3);
                $order->kode_unique = $kode_unique;

                $param_trx_account = [
                    'no_transaksi' => 'AC-'.rand(100000000, 999999999),
                    'uuid_user' => Auth::user()->uuid,
                    'type_payment' => $request['type_payment'],
                    'method' => $request['method'],
                    'jns_payment' => 'DEBIT',
                    'biaya_trx' => $order->total_biaya,
                    'total' => $order->total_biaya,
                    'no_refrensi' => $order->no_order,
                    'kode_unique' => $order->kode_unique,
                    'keterangan' => 'Order Produk'
                ];
                
                TransaksiAccount::create($param_trx_account);
                $notification_admin = array(
                    'uuid' => Str::uuid(32),
                    'type' => 'konfirmasi-pembayaran',
                    'target' => 'konfirmasi-pembayaran',
                    'value' => json_encode($param_trx_account),
                    'status_read' => 0
                );
                
                NotifikasiAdmin::create($notification_admin);
                
                event(new NotificationAdmin($notification_admin));
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
                
                $biaya_platform = $this->settings_web->biaya_platform;
                $biaya_platform = (float) $biaya_platform / 100;
                $biaya_platform = (float) $produk->getHargaFixed() * $biaya_platform;

                $detail = new DetailOrder();
                $detail->no_order = $order->no_order;
                $detail->kode_produk = $item['kode_produk'];
                $detail->uuid_user = Auth::user()->uuid;
                $detail->kode_toko = $produk->toko->kode_toko;
                $detail->kode_referal = $cart->referal;
                $detail->type_produk = $produk->type_produk;
                $detail->biaya = $produk->harga;
                $detail->total_biaya = (float) ($produk->getHargaFixed() - $biaya_platform);
                $detail->potongan_diskon = $total_potongan;
                $detail->potongan = $total_potongan + $biaya_platform;
                $detail->potongan_platform = $biaya_platform;
                
                if($cart->referal) {
                    $detail->potongan_referal = (float) ($produk->komisi_referal / 100) * $produk->getHargaFixed();
                    $detail->total_biaya = (float) $detail->total_biaya - $detail->potongan_referal;
                }

                if($request['type_payment'] == 'iorpay') {
                    if($produk->type_produk == 'AUTO') {
                        $detail->status_order = 'SUCCESS';
                    }
                }

                $detail->quantity = 1;
                $detail->save();

                $param_pendapatan = [
                    'no_trx' => 'PD-'.rand(100000000, 999999999),
                    'type' => $request['type_payment'],
                    'account' => ($request['type_payment'] == 'iorpay' ? 'PAY' : ($request['type_payment'] == 'gateway' ? 'GATEWAY' : $request['method'])),
                    'biaya' => $detail->total_biaya,
                    'pendapatan' => $detail->potongan_platform,
                    'no_refrensi' => $order->no_order,
                    'status' => (isset($order->status_order) ? $order->status_order : 'PENDING'),
                    'type_payment' => 'DEBIT'
                ];

                Pendapatan::create($param_pendapatan);

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
                
                $notification = array(
                    'uuid' => Str::uuid(32),
                    'to' => $produk->toko->user->uuid,
                    'from' => Auth::user()->uuid,
                    'type' => 'order_toko',
                    'value' => $detail,
                    'status_read' => 0
                );

                $notification_admin = array(
                    'uuid' => Str::uuid(32),
                    'type' => 'daftar-order',
                    'target' => 'daftar-order',
                    'value' => $detail,
                    'status_read' => 0
                );

                NotifikasiAdmin::create($notification_admin);
                
                Notif::create($notification);
                event(new NotifikasiOrderToko($notification));
                event(new NotificationAdmin($notification_admin));
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

                            $trx_komisi = new TransaksiKomisiReferal();
                            $trx_komisi->no_trx = $trx_pay->no_trx;
                            $trx_komisi->kode_produk = $produk->kode_produk;
                            $trx_komisi->kode_pay = $pay_referal->kode_pay;
                            $trx_komisi->total_komisi = $produk->komisi_referal;
                            $trx_komisi->save();
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
                            $komisi = (float) ($produk->komisi_referal / 100);
                            $komisi = (float) ($produk->harga * $komisi);
                            $pay_referal->saldo += $komisi;
                            $pay_referal->save();

                            $trx_pay = new TrxIorPay();
                            $trx_pay->no_trx = 'TRX-'.rand(100000000, 999999999);
                            $trx_pay->kode_pay = $pay_referal->kode_pay;
                            $trx_pay->uuid_user = $pay_referal->user->uuid;
                            $trx_pay->type_pay = 'DEBIT';
                            $trx_pay->jenis_pembayaran = 'REFERAL';
                            $trx_pay->total_trx = $komisi;
                            $trx_pay->total_fixed = $komisi;
                            $trx_pay->keterangan = 'Komisi Referal';
                            $trx_pay->status_trx = 'SUCCESS';
                            $trx_pay->save();

                            $trx_komisi = new TransaksiKomisiReferal();
                            $trx_komisi->no_trx = $trx_pay->no_trx;
                            $trx_komisi->kode_produk = $produk->kode_produk;
                            $trx_komisi->kode_pay = $pay_referal->kode_pay;
                            $trx_komisi->total_komisi = $komisi;
                            $trx_komisi->save();

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
            $trx_topup = TrxIorPay::where('no_trx', $orderId)->first();
            error_log("Order ID $notif->order_id: "."transaction status = $transaction, fraud staus = $fraud");
            if(isset($order)) {
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
                            $biaya_platform = (float) ($this->settings_web->biaya_platform / 100);
                            $potongan_platform = (float) ($harga_fixed * $biaya_platform);
                            $pendapatan_toko = (float) ($harga_fixed - $potongan_platform);
                            
                            if($detail->kode_referal) {
                                // ambil iorpay yang menshare link
                                if($produk->status_referal) {
                                    $pay_referal = IorPay::where('uuid_user', $detail->kode_referal)->first();
                                    $komisi = (float) ($produk->komisi_referal / 100);
                                    $komisi = (float) ($produk->harga * $komisi);
                                    $pay_referal->saldo += $komisi;
                                    $pay_referal->save();
    
                                    $trx_pay = new TrxIorPay();
                                    $trx_pay->no_trx = 'TRX-'.rand(100000000, 999999999);
                                    $trx_pay->kode_pay = $pay_referal->kode_pay;
                                    $trx_pay->uuid_user = $pay_referal->user->uuid;
                                    $trx_pay->type_pay = 'DEBIT';
                                    $trx_pay->jenis_pembayaran = 'REFERAL';
                                    $trx_pay->total_trx = $komisi;
                                    $trx_pay->total_fixed = $komisi;
                                    $trx_pay->keterangan = 'Komisi Referal';
                                    $trx_pay->status_trx = 'SUCCESS';
                                    $trx_pay->save();
    
                                    $trx_komisi = new TransaksiKomisiReferal();
                                    $trx_komisi->no_trx = $trx_pay->no_trx;
                                    $trx_komisi->kode_produk = $produk->kode_produk;
                                    $trx_komisi->kode_pay = $pay_referal->kode_pay;
                                    $trx_komisi->total_komisi = $komisi;
                                    $trx_komisi->save();
                                }
                            }
                            if($produk->type_produk == 'AUTO') {
                                SaldoRefaund::addSaldo($produk->kode_toko, $pendapatan_toko);
                                ClearingSaldo::create([
                                    'kode_toko' => $produk->kode_toko,
                                    'saldo' => $pendapatan_toko,
                                    'tanggal_insert' => now()->format('Y-m-d'),
                                    'jadwal_clear' => Carbon::now()->addDay(3)->format('Y-m-d')
                                ]);

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
    
                                if($ot->type_produk == 'AUTO') {
                                    $ot->status_order = 'SUCCES';
                                    $ot->save();
                                }
                            }
    
                            $biaya_platform = (float) ($this->settings_web->biaya_platform / 100);
                            $potongan_platform = (float) ($total_pembayaran_pertoko * $biaya_platform);
                            $pendapatan_toko = (float) ($total_pembayaran_pertoko - $potongan_platform);
    
                            $get_order_toko->total_biaya = $pendapatan_toko;
                            $user_toko = User::where('uuid', $daftar_toko->user->uuid)->first();
                            SendInvoiceToko::dispatch($user_toko, $get_order_toko);
                        }
    
                        $trx_pendapatan = Pendapatan::where([
                            'no_refrensi' => $order->no_order,
                            'status' => 'PENDING',
                        ])->get();
    
                        if(isset($trx_pendapatan)) {
                            foreach($trx_pendapatan as $pd) {
                                $pd->status = 'SUCCESS';
                            }
                        }
    
                        $param_trx_account = [
                            'no_transaksi' => 'AC-'.rand(100000000, 999999999),
                            'uuid_user' => $order->uuid_user,
                            'type_payment' => $order->type_payment,
                            'method' => $paymentType,
                            'jns_payment' => 'DEBIT',
                            'biaya_trx' => $order->total_biaya,
                            'total' => $order->total_biaya,
                            'no_refrensi' => $order->no_order,
                            'kode_unique' => $order->kode_unique,
                            'keterangan' => 'Order Produk'
                        ];
        
                        TransaksiAccount::create($param_trx_account);
    
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
                            $biaya_platform = (float) ($this->settings_web->biaya_platform / 100);
                            $potongan_platform = (float) ($harga_fixed * $biaya_platform);
                            $pendapatan_toko = (float) ($harga_fixed - $potongan_platform);
    
                            if($detail->kode_referal) {
                                // ambil iorpay yang menshare link
                                if($produk->status_referal) {
                                    $pay_referal = IorPay::where('uuid_user', $detail->kode_referal)->first();
                                    $komisi = (float) ($produk->komisi_referal / 100);
                                    $komisi = (float) ($produk->harga * $komisi);
                                    $pay_referal->saldo += $komisi;
                                    $pay_referal->save();
    
                                    $trx_pay = new TrxIorPay();
                                    $trx_pay->no_trx = 'TRX-'.rand(100000000, 999999999);
                                    $trx_pay->kode_pay = $pay_referal->kode_pay;
                                    $trx_pay->uuid_user = $pay_referal->user->uuid;
                                    $trx_pay->type_pay = 'DEBIT';
                                    $trx_pay->jenis_pembayaran = 'REFERAL';
                                    $trx_pay->total_trx = $komisi;
                                    $trx_pay->total_fixed = $komisi;
                                    $trx_pay->keterangan = 'Komisi Referal';
                                    $trx_pay->status_trx = 'SUCCESS';
                                    $trx_pay->save();
    
                                    $trx_komisi = new TransaksiKomisiReferal();
                                    $trx_komisi->no_trx = $trx_pay->no_trx;
                                    $trx_komisi->kode_produk = $produk->kode_produk;
                                    $trx_komisi->kode_pay = $pay_referal->kode_pay;
                                    $trx_komisi->total_komisi = $komisi;
                                    $trx_komisi->save();
                                }
                            }
                            if($produk->type_produk == 'AUTO') {
                                SaldoRefaund::addSaldo($produk->kode_toko, $pendapatan_toko);
                                ClearingSaldo::create([
                                    'kode_toko' => $produk->kode_toko,
                                    'saldo' => $pendapatan_toko,
                                    'tanggal_insert' => now()->format('Y-m-d'),
                                    'jadwal_clear' => Carbon::now()->addDay(3)->format('Y-m-d')
                                ]);

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
    
                                if($ot->type_produk == 'AUTO') {
                                    $ot->status_order = 'SUCCES';
                                    $ot->save();
                                }
                            }
    
                            $biaya_platform = (float) ($this->settings_web->biaya_platform / 100);
                            $potongan_platform = (float) ($total_pembayaran_pertoko * $biaya_platform);
                            $pendapatan_toko = (float) ($total_pembayaran_pertoko - $potongan_platform);
    
                            $data['order'] = $get_order_toko;
                            $data['total_biaya'] = $pendapatan_toko;
                            $user_toko = User::where('uuid', $daftar_toko->user->uuid)->first();
                            SendInvoiceToko::dispatch($user_toko, $data);
                        }
    
                        $trx_pendapatan = Pendapatan::where([
                            'no_refrensi' => $order->no_order,
                            'status' => 'PENDING',
                        ])->get();
    
                        if(isset($trx_pendapatan)) {
                            foreach($trx_pendapatan as $pd) {
                                $pd->status = 'SUCCESS';
                                $pd->save();
                            }
                        }
    
                        $param_trx_account = [
                            'no_transaksi' => 'AC-'.rand(100000000, 999999999),
                            'uuid_user' => $order->uuid_user,
                            'type_payment' => $order->type_payment,
                            'method' => $paymentType,
                            'jns_payment' => 'DEBIT',
                            'biaya_trx' => $order->total_biaya,
                            'total' => $order->total_biaya,
                            'no_refrensi' => $order->no_order,
                            'kode_unique' => $order->kode_unique,
                            'keterangan' => 'Order Produk'
                        ];
        
                        TransaksiAccount::create($param_trx_account);
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
            }else {
                if ($transaction == 'capture') {
                    if ($fraud == 'challenge') {
                        $trx_topup->status_trx = 'PENDING';
                    }
                    else if ($fraud == 'accept') {
                        // Lakukan penambahan saldo ke iorpay user
                        $iorpay_user = IorPay::where([
                            'kode_pay' => $trx_topup->kode_pay,
                            'uuid_user' => $trx_topup->uuid_user,
                            'status_pay' => 1
                        ])->first();
                        
                        // cek biaya admin
                        $biaya_admin = intval($trx_topup->biaya_adm);
                        $total_saldo = (float) $trx_topup->total_fixed - $biaya_admin;
                        $iorpay_user->saldo += $total_saldo;
                        
                        if($iorpay_user->save()) {
                            $trx_topup->status_trx = 'SUCCESS';
                            $trx_topup->save();

                            $param_trx_account = [
                                'no_transaksi' => 'AC-'.rand(100000000, 999999999),
                                'uuid_user' => $trx_topup->uuid_user,
                                'type_payment' => 'manual',
                                'method' => $trx_topup->jenis_pembayaran,
                                'jns_payment' => 'DEBIT',
                                'biaya_trx' => $trx_topup->total_fixed,
                                'total' => $trx_topup->total_fixed,
                                'no_refrensi' => $trx_topup->no_trx,
                                'kode_unique' => $trx_topup->kode_unique,
                                'keterangan' => 'Topup Saldo'
                            ];

                            TransaksiAccount::create($param_trx_account);
                        }
    
                    }
                }
                else if ($transaction == 'cancel') {
                    if ($fraud == 'challenge') {
                        $trx_topup->status_trx = 'PENDING';
                    }
                    else if ($fraud == 'accept') {
                    // TODO Set payment status in merchant's database to 'failure'
                        $trx_topup->status_trx = 'CANCEL';
                    }
                }
                else if ($transaction == 'pending') {
                    if ($fraud == 'challenge') {
                        $trx_topup->status_trx = 'PENDING';
                    }
                    else if ($fraud == 'accept') {
                    // TODO Set payment status in merchant's database to 'failure'
                        $trx_topup->status_trx = 'PENDING';
                    }
                }
                else if ($transaction == 'settlement') {
                    if ($fraud == 'challenge') {
                        $trx_topup->status_trx = 'PENDING';
                    }else if ($fraud == 'accept') {
                        // Lakukan penambahan saldo ke iorpay user
                        $iorpay_user = IorPay::where([
                            'kode_pay' => $trx_topup->kode_pay,
                            'uuid_user' => $trx_topup->uuid_user,
                            'status_pay' => 1
                        ])->first();
                        
                        // cek biaya admin
                        $biaya_admin = intval($trx_topup->biaya_adm);
                        $total_saldo = (float) $trx_topup->total_fixed - $biaya_admin;
                        $iorpay_user->saldo += $total_saldo;
                        
                        if($iorpay_user->save()) {
                            $trx_topup->status_trx = 'SUCCESS';

                            $param_trx_account = [
                                'no_transaksi' => 'AC-'.rand(100000000, 999999999),
                                'uuid_user' => $trx_topup->uuid_user,
                                'type_payment' => 'gateway',
                                'method' => $paymentType,
                                'jns_payment' => 'DEBIT',
                                'biaya_trx' => $trx_topup->total_fixed,
                                'total' => $trx_topup->total_fixed,
                                'no_refrensi' => $trx_topup->no_trx,
                                'keterangan' => 'Topup Saldo'
                            ];

                            TransaksiAccount::create($param_trx_account);
                        }
                    }
                }
                else if ($transaction == 'deny') {
                    $trx_topup->status_trx = 'CANCEL';
                }
                else if ($transaction == 'expire') {
                    $trx_topup->status_trx = 'EXPIRED';
                }
                $trx_topup->method = $paymentType;
                $trx_topup->save();
            }

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
