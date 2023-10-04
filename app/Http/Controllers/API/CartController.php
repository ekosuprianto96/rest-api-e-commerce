<?php

namespace App\Http\Controllers\API;

use App\Models\Cart;
use App\Models\Produk;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\Handle\ErrorController;

class CartController extends Controller
{
    public function show() {
        try {

            $cart = Cart::selectRaw('count(carts.kode_produk) as quantity, produk.*, detail_toko.kode_toko, detail_toko.nama_toko, detail_toko.image as image_toko, kategori.nama_kategori')
                        ->join('produk', 'carts.kode_produk', 'produk.kode_produk')
                        ->join('kategori', 'produk.kode_kategori', 'kategori.kode_kategori')
                        ->join('detail_toko', 'produk.kode_toko', 'detail_toko.kode_toko')
                        ->where('carts.uuid_user', Auth::user()->uuid)
                        ->groupBy('produk.kode_produk')
                        ->get();
            foreach($cart as $c) {
                $produk = Produk::with(['kategori', 'toko'])->where([
                    'kode_produk' => $c->kode_produk,
                    'an' => 1
                ])->first();
                $c->harga = $produk->getHargaDiskon($produk);
                $wishlist = Wishlist::where([
                    'kode_produk' => $c->kode_produk,
                    'uuid_user' => auth()->guard('api')->user()->uuid
                ])->first();

                if($wishlist) {
                    if($c->kode_produk == $wishlist->kode_produk) {
                        $c->wishlist = 1;
                    }else {
                        $c->wishlist = 0;
                    }
                }else {
                    $c->wishlist = 0;
                }
            }
            
            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Get data success fully',
                'detail' => $cart
            ], 200);

        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function store(Request $request) {
        try {
            // Check Cart
            $check_cart = Cart::where([
                'kode_produk' => $request['kode_produk'],
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
            $cart = new Cart();
            $cart->kode_produk = $request['kode_produk'];
            $cart->uuid_user = Auth::user()->uuid;
            $cart->referal = (isset($request['referal']) ? $request['referal'] : NULL);
            
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

    public function destroy(Request $request) {
        try {
            $cart = Cart::where([
                'kode_produk' => $request['kode_produk'],
                'uuid_user' => $request['uuid_user']
            ])->first();
    
            if($cart) {
                $cart->delete();
                return response()->json([
                    'status' => true,
                    'error' => false,
                    'message' => 'Yah, Produk Kamu Berhasil Terhapus.',
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
}
