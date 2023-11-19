<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Order;
use App\Models\IorPay;
use App\Models\Produk;
use App\Models\TrxIorPay;
use App\Models\DetailToko;
use App\Models\DetailOrder;
use Illuminate\Support\Str;
use App\Models\SaldoRefaund;
use Illuminate\Http\Request;
use App\Jobs\SendInvoiceToko;
use App\Models\AksesDownload;
use App\Models\ClearingSaldo;
use App\Models\PaymentMethod;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Models\TransaksiKomisiReferal;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\API\Handle\ErrorController;
use App\Models\Pendapatan;

class PaymentController extends Controller
{
    public function index() {
        return view('admin.payment.index');
    }

    public function detail($no_order) {

        $order = Order::where('no_order', $no_order)->first();
        return view('admin.payment.detail', compact('order'));

    }

    public function konfirmasi(Request $request) {
        try {

            $order = Order::where([
                    'type_payment' => 'manual',
                    'status_order' => 0,
                    'no_order' => $request['no_order']
                ])->first();

            if($order->status_order == 'SUCCESS') {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Mohon Maaf, Pembayaran Sudah Di Konfirmasi.',
                    'detail' => $order
                ]);
            }
            
            $daftar_order_toko = array();
            $order->status_order = 'SUCCESS';

            $pendapatan = Pendapatan::where('no_refrensi', $order->no_order)->get();

            if(isset($pendapatan)) {
                foreach($pendapatan as $pd) {
                    $pd->status = $order->status_order;
                    $pd->save();
                }
            }

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
                    if($ot->type_produk == 'AUTO') {
                        $ot->status_order = 'SUCCES';
                        $ot->save();
                    }
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
            
            $order->save();

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => "Berhasil Konfirmasi Pembayaran, \nNo Order: $order->no_order",
                'detail' => $order
            ]);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function show() {
        $payment = PaymentMethod::where('status_payment', 1)->get();

        return response()->json([
            'status' => true,
            'error' => false,
            'message' => 'Berhasil get payment',
            'detail' => $payment
        ], 200);
    }

    public function konfirmasi_data(Request $request) {
        try {
            $order = Order::latest()->whereRaw("status_order = '0' and type_payment = 'manual'")->get();

            $data = DataTables::of($order)
                        ->addColumn('no_order', function($list) {
                            return $list->no_order;
                        })
                        ->addColumn('customer', function($list) {
                            return $list->user->full_name;
                        })
                        ->addColumn('total_produk', function($list) {
                            return $list->detail->count();
                        })
                        ->addColumn('total_biaya', function($list) {
                            return 'Rp. '.number_format($list->total_biaya, 0);
                        })
                        ->addColumn('total_potongan', function($list) {
                            return 'Rp. '.number_format($list->total_potongan, 0);
                        })
                        ->addColumn('kode_unique', function($list) {
                            return (isset($list->kode_unique) ? $list->kode_unique : '-');
                        })
                        ->addColumn('action', function($list) {
                            return '<div class="d-flex align-items-center justify-content-center" style="gap: 7px;">
                                        <a href="'.route('admin.payment.detail', $list->no_order).'" class="btn btn-sm btn-primary text-nowrap" style="font-size: 0.8em"><i class="fa fa-eye"></i> Detail</a>
                                        <a href="javascript:void(0)" onclick="konfirmasi_payment('."'".$list->no_order."'".')" class="btn btn-sm btn-success text-nowrap" style="font-size: 0.8em"><i class="fa fa-eye"></i> Konfirmasi</a>
                                    </div>';
                        })
                        ->rawColumns(['no_order', 'customer', 'total_produk', 'total_biaya', 'total_potongan', 'kode_unique', 'action'])
                        ->make(true);

            return $data;
        }catch(\Exception $err) {
            return response()->json([
                'status' => false,
                'error' => true,
                'message' => $err->getMessage(),
                'details' => 'Line :'.$err->getLine() 
            ], 500);
        }
    }
}
