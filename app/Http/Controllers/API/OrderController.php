<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use App\Models\Produk;
use App\Models\DetailOrder;
use Illuminate\Http\Request;
use App\Models\AksesDownload;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\KonfirmasiPembayaran;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\Handle\ErrorController;

class OrderController extends Controller
{
    public function show(Request $request) {
        
        $order = Order::where('uuid_user', $request['uuid_user'])->latest()->get();

        foreach($order as $or) {
            $or->total_biaya = number_format($or->total_biaya, 0);
        }
        return response()->json([
            'status' => true,
            'error' => false,
            'message' => 'Berhasil Get Order',
            'detail' => $order
        ], 200);
    }
    public function checkout_manual(Request $request) {
        try {
            DB::beginTransaction();
            $order = Order::where('no_order', $request['no_order'])->first();

            $detail = DetailOrder::where('no_order', $order->no_order)->get();

            $payment = PaymentMethod::where('kode_payment', $order->payment_method)->first();
            $order->total_biaya = number_format($order->total_biaya, 0);
            $order->total_potongan = number_format($order->total_potongan, 0);

            foreach($detail as $d) {
                $produk = Produk::where('kode_produk', $d->kode_produk)->first();
                $produk->harga = $produk->getHargaDiskon($produk);
                $d['produk'] = $produk;
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil Gaet Order',
                'detail' => [
                    'order' => $order,
                    'detail' => $detail,
                    'payment' => $payment
                ]
            ], 200);
        }catch(\Exception $err) {
            DB::rollback();
            return ErrorController::getResponseError($err);
        }
    }
    public function checkout(Request $request) {

        try {
            DB::beginTransaction();
            $order = Order::where('snap_token', $request['snap_token'])->first();
            $detail = DetailOrder::where('no_order', $order->no_order)->get();
            $order->total_biaya = number_format($order->total_biaya, 2);
            $order->total_potongan = number_format($order->total_potongan, 2);
            foreach($detail as $d) {
                $produk = Produk::where('kode_produk', $d->kode_produk)->first();
                $produk->harga = $produk->getHargaDiskon($produk);
                $d['produk'] = $produk;
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil Gaet Order',
                'detail' => [
                    'order' => $order,
                    'detail' => $detail
                ]
            ], 200);
        }catch(\Exception $err) {
            DB::rollback();
            return ErrorController::getResponseError($err);
        }
    }

    public function cancel(Request $request) {
        try {
            $order = Order::where([
                'no_order' => $request['no_order'],
                'uuid_user' => $request['uuid_user']
            ])->first();
    
            if($order) {
                $order->status_order = 'CANCEL';
                $order->save();
                return response()->json([
                    'status' => true,
                    'error' => false,
                    'message' => 'Yah, Sedih Melihatnya, Tapi Order Kamu Berhasil Dicancel.',
                    'detail' => []
                ]);
            }

            return response()->json([
                'status' => false,
                'error' => true,
                'message' => 'Ada masalah Kecil, Silahkan Coba Lagi.',
                'detail' => []
            ]);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function akses(Request $request) {
        try {
            $akses = AksesDownload::with('produk')->where('uuid_user', $request['uuid_user'])->get();

            if(empty($request['uuid_user']) || $request['uuid_user'] != Auth::user()->uuid) {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Forbidden',
                ], 403);
            }
            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil get akses',
                'detail' => $akses
            ], 200);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function konfirmasi(Request $request) {

        $order = Order::with(['detail'])->where([
            'no_order' => $request['no_order'],
            'uuid_user' => $request['uuid_user']
        ])->first();
        
        $konfirmasi = KonfirmasiPembayaran::where('no_order', $request['no_order'])->first();

        if($konfirmasi) {
            $konfirmasi->delete();
        }

        KonfirmasiPembayaran::create([
            'no_order' => $request['no_order'],
            'kode_payment' => $request['payment_method'],
            'total_biaya' => $order->total_biaya,
            'total_potongan' => $order->total_potongan,
            'biaya_platform' => 10
        ]);

        return response()->json([
            'status' => true,
            'error' => false,
            'message' => 'Berhasil get order',
            'detail' => $order
        ]);
    }
}
