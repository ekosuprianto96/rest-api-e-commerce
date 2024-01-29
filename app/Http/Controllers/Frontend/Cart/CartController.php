<?php

namespace App\Http\Controllers\Frontend\Cart;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\Handle\ErrorController;
use App\Models\Produk;
use App\Models\ReferalTempUser;

class CartController extends Controller
{

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
}
