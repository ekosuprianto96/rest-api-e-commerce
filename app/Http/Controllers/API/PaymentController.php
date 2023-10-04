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
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Controllers\API\Handle\ErrorController;

class PaymentController extends Controller
{
    public function index() {

        $order = Order::latest()->whereRaw("status_order = '0' and type_payment = 'manual'")->paginate(50);
        
        return view('admin.payment.index', compact('order'));
    }

    public function detail($no_order) {

        $order = Order::where('no_order', $no_order)->first();
        return view('admin.payment.detail', compact('order'));

    }

    public function konfirmasi(Request $request, $no_order) {
        try {

            $order = Order::where([
                    'type_payment' => 'manual',
                    'status_order' => 0,
                    'no_order' => $no_order
                ])->first();

            if($order->status_order == 'SUCCESS') {
                Alert::success('Perhatian!', 'Order Sudah Dikonfirmasi');
                return redirect()->back();
            }

            $daftar_order_toko = array();
            $order->status_order = 'SUCCESS';
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

            $order->save();

            Alert::success('Sukses!', 'Berhasil Konfirmasi Pembayaran');

            return redirect()->back();
        }catch(\Exception $err) {
            return ErrorController::getError($err);
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
}
