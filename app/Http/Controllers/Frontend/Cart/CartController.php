<?php

namespace App\Http\Controllers\Frontend\Cart;

use Midtrans\Snap;
use App\Models\Cart;
use Midtrans\Config;
use App\Models\Order;
use App\Models\IorPay;
use App\Models\Produk;
use App\Models\TrxIorPay;
use App\Models\Pendapatan;
use App\Models\DetailOrder;
use Illuminate\Support\Str;
use App\Models\SaldoRefaund;
use Illuminate\Http\Request;
use App\Models\AksesDownload;
use App\Models\ClearingSaldo;
use App\Models\SettingGateway;
use App\Models\SettingWebsite;
use Illuminate\Support\Carbon;
use App\Models\NotifikasiAdmin;
use App\Models\ReferalTempUser;
use App\Models\TransaksiAccount;
use App\Events\NotificationAdmin;
use Illuminate\Support\Facades\DB;
use App\Events\NotifikasiOrderToko;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\TransaksiKomisiReferal;
use App\Http\Controllers\API\Handle\ErrorController;
use App\Models\Notification as Notif;
use Illuminate\Support\Facades\Http;

class CartController extends Controller
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

    public function store(Request $request) {
        try {

            $user = Auth::user();
            
            $check_cart = Cart::where([
                'kode_produk' => $request->kodeproduk,
                'uuid_user' => Auth::user()->uuid
            ])->first();

            if(isset($check_cart)) {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Produk Sudah Ada Di Keranjang.',
                    'detail' => []
                ]);
            }

            $cekAfiliasi = ReferalTempUser::where([
                'uuid_user' => $user->uuid,
                'kode_produk' => $request->kodeproduk
            ])->first();

            $cart = new Cart();
            $cart->kode_produk = $request->kodeproduk;
            $cart->uuid_user = $user->uuid;
            $cart->referal = isCheckVar($cekAfiliasi, 'kode_referal');
            
            if($cart->save()) {
                return response()->json([
                    'status' => true,
                    'error' => false,
                    'message' => 'Hore!, Produk Kamu Berhasil Ditambahkan Ke Keranjang.',
                    'detail' => $cart
                ]);
            }
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function checkout(Request $request) {
        try {
            
            DB::beginTransaction();
            $items = array();
            $total = 0;
            $total_potongan = 0;
            $potongan_referal = 0;
            // Hitung Total Biaya Dan Potongan
            foreach($request->kode_produk as  $item) {
                $produk = Produk::where('kode_produk', $item)->first();
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
            $order->quantity = count($request->kode_produk);
            $order->type_payment = $request->typePayment;
            
            $order->payment_method = $request->bank ?? null;
            if($request->typePayment == 'manual') {
                $order->total_biaya = (float) $total + $kode_unique;
                $kode_unique = substr($order->total_biaya, -3);
                $order->kode_unique = $kode_unique;

                $param_trx_account = [
                    'no_transaksi' => 'AC-'.rand(100000000, 999999999),
                    'uuid_user' => Auth::user()->uuid,
                    'type_payment' => $request->typePayment,
                    'method' => $request->bank ?? null,
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
            
            if($request->typePayment == 'linggaPay') {
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
                        $trx_pay->jenis_pembayaran = $request->typePayment;
                        $trx_pay->total_trx = $order->total_biaya;
                        $trx_pay->total_fixed = $order->total_biaya;
                        $trx_pay->keterangan = 'Pembelian Produk';
                        $trx_pay->status_trx = 'SUCCESS';
                        $trx_pay->save();

                        $order->status_order = 'SUCCESS';

                        foreach($request->kode_produk as $item) {
                            $produk = Produk::where('kode_produk', $item)->first();
                            
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

            $order->total_potongan = (float) $total_potongan;
            
            foreach($request->kode_produk as  $item) {
                $produk = Produk::where('kode_produk', $item)->first();
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
                                'kode_produk' => $item,
                                'uuid_user' => Auth::user()->uuid
                            ])->first();
                
                $biaya_platform = $this->settings_web->biaya_platform;
                $biaya_platform = (float) $biaya_platform / 100;
                $biaya_platform = (float) $produk->getHargaFixed() * $biaya_platform;

                $detail = new DetailOrder();
                $detail->no_order = $order->no_order;
                $detail->kode_produk = $item;
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

                if($request->typePayment == 'linggaPay') {
                    if($produk->type_produk == 'AUTO') {
                        $detail->status_order = 'SUCCESS';
                    }
                }

                $detail->quantity = 1;
                $detail->save();

                $paramTransaksiAffiliasi = [
                    'kode_produk' => $produk->kode_produk,
                    'uuid_user' => Auth::user()->uuid,
                    'type_payment' => $request->typePayment,
                    'total_komisi' => $detail->potongan_referal,
                    'no_order' => $detail->no_order,
                    'id_order' => $detail->id
                ];

                $assignKomisiAaffiliasi = (new TransaksiKomisiReferal())->addTransaksiAffiliasi($paramTransaksiAffiliasi);
                if(!$assignKomisiAaffiliasi['status']) {
                    DB::rollBack();
                    return response()->json([
                        'status' => true,
                        'error' => true,
                        'message' => $assignKomisiAaffiliasi['message'],
                        'detail' => $assignKomisiAaffiliasi['detail']
                    ]);
                }

                $param_pendapatan = [
                    'no_trx' => 'PD-'.rand(100000000, 999999999),
                    'type' => $request->typePayment,
                    'account' => ($request->typePayment == 'linggaPay' ? 'PAY' : ($request->typePayment == 'gateway' ? 'GATEWAY' : $request->bank ?? null)),
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
                    'name' => $produk->nm_produk
                ]);
                
                Cart::where([
                    'kode_produk' => $item,
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

                $http = Http::post(env('URL_SOCKET_LINGGA').'/api/message/post', [
                    'uuid' => Str::uuid(),
                    'message' => 'Ada order masuk',
                    'kode_toko' => $produk->toko->kode_toko
                ]);

                if(!$http->successful()) {
                    DB::rollBack();
                    return response()->json([
                        'status' => true,
                        'error' => true,
                        'message' => 'terjadi kesalahan pada saat mengirim pesan order'
                    ], 500);
                }

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

            if($request->typePayment == 'gateway') {
                $snapToken = Snap::getSnapToken($params);
            }

            $order->snap_token = ($request->typePayment == 'gateway' ? $snapToken : NULL);
            $order->save();
        
            DB::commit();
            return response()->json([
                'status' => true,
                'error' => false,
                'detail_token' => ($request->typePayment == 'gateway' ? $snapToken : $order->no_order),
                'detail' => [
                    'redirect' => route('user.dashboard', getUserName())
                ],
                'message' => 'Selamat, Transaksi Anda Berhasil.'
            ], 200);
        }catch(\Exception $err) {
            DB::rollBack();
            return ErrorController::getResponseError($err);
        }
    }

    public function transaksiReferal(Request $request) {
        try {

            foreach($request->kode_produk as $item) {
                $produk = Produk::where('kode_produk', $item)->first();
                if($request->typePayment == 'manual' || $request->typePayment == 'gateway') {
    
                    $cart = Cart::where([
                        'kode_produk' => $item,
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
                        'kode_produk' => $item,
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
}
