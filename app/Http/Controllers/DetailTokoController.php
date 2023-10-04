<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Helper\Helper;
use App\Models\DetailToko;
use App\Models\DetailOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\Handle\ErrorController;

class DetailTokoController extends Controller
{
    public function detail() {
        $detail = DetailToko::with('saldo')->where('uuid_user', Auth::user()->uuid)->first();
        $detail->saldo->total_saldo = number_format($detail->saldo->total_saldo, 2, '.');
        $detail->saldo_refaund->total_refaund = number_format($detail->saldo_refaund->total_refaund, 2, '.');
        return response()->json([
            'status' => true,
            'error' => false,
            'message' => 'Berhasil get detail toko',
            'detail' => $detail
        ], 200);
    }

    public function update(Request $request) {
        $request->validate([
            'nama_toko' => 'required|string|min:6',
            'alamat_toko' => 'required|string|min:6|max:50',
        ]);

        try {
            $toko = DetailToko::where('uuid_user', Auth::user()->uuid)->first();
            if($toko) {
                $toko->nama_toko = $request['nama_toko'];
                $toko->alamat_toko = $request['alamat_toko'];
                $toko->jam_buka = $request['jam_buka'];
                $toko->jam_tutup = $request['jam_tutup'];
                $toko->save();
                return response()->json([
                    'status' => true,
                    'error' => false,
                    'message' => 'Berhasil Update Toko',
                    'detail' => $toko
                ], 200);
            }else {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Gagal Update Akun, Silahkan Periksa Kembali Form Anda.',
                    'detail' => []
                ], 400);
            }
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function upload_image(Request $request) {
        $request->validate([
            'image' => 'required|image|mimes:jpg,png,svg,jpeg,webp|max:30000'
        ]);

        try {
            if($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $image = asset('assets/toko/image/'.Auth::user()->toko->kode_toko.'/'.$fileName);
                $file->move(public_path('assets/toko/image/'.Auth::user()->toko->kode_toko), $fileName);
                DetailToko::where('uuid_user', Auth::user()->uuid)->update([
                    'image' => $image
                ]);

                return response()->json([
                    'status' => true,
                    'error' => false,
                    'message' => 'Berhasil Update Foto.',
                    'image' => $image
                ], 200);
            }
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function order($kode_toko) {

        $order_toko = Order::selectRaw('orders.*, users.full_name as nama_pembeli')
                            ->join('users', 'orders.uuid_user', 'users.uuid')
                            ->join('detail_orders', 'orders.no_order', 'detail_orders.no_order')
                            ->where('kode_toko', $kode_toko)
                            ->latest()
                            ->get()->groupBy('no_order');
        $orders = array();
        foreach($order_toko as $key => $order) {
            $or = DetailOrder::with(['user'])->where('no_order', $key)->get();
            $order_detail = Order::where('no_order', $key)->first();
            
            $total_biaya = number_format(Helper::tambahBiayaPlatform($order_detail->total_biaya), 2);
            $item = array(
                'no_order' => $order_detail->no_order,
                'status_order' => $order_detail->status_order,
                'total_biaya' => $total_biaya,
                'tanggal' => $order_detail->created_at->format('d-m-Y'),
                'biaya_platform' => '10%',
                'detail' => $or
            );

            array_push($orders, $item);
        } 
        return response()->json([
            'status' => true,
            'error' => false,
            'message' => 'Berhasil get order toko.',
            'detail' => $orders
        ], 200);
    }
}
