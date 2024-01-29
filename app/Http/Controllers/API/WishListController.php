<?php

namespace App\Http\Controllers\API;

use App\Models\Cart;
use App\Models\Produk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\Handle\ErrorController;
use App\Models\Wishlist;

class WishListController extends Controller
{
    public function show() {
        try {

            $wishlist = Wishlist::selectRaw('count(wishlists.kode_produk) as quantity, produk.*, detail_toko.kode_toko, detail_toko.nama_toko, detail_toko.image as image_toko, kategori.nama_kategori')
                        ->join('produk', 'wishlists.kode_produk', 'produk.kode_produk')
                        ->join('kategori', 'produk.kode_kategori', 'kategori.kode_kategori')
                        ->join('detail_toko', 'produk.kode_toko', 'detail_toko.kode_toko')
                        ->where('wishlists.uuid_user', Auth::user()->uuid)
                        ->where('produk.an', 1)
                        ->groupBy('produk.kode_produk')
                        ->get();
            foreach($wishlist as $c) {
                $produk = Produk::with(['kategori', 'toko'])->where([
                    'kode_produk' => $c->kode_produk
                ])->first();
                $c->harga = $produk->getHargaDiskon();
                $c->form = $produk->form;
            }
            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Get data success fully',
                'detail' => $wishlist
            ], 200);

        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function store(Request $request) {
        try {
            // Check Cart
            $check_wishlist = Wishlist::where([
                'kode_produk' => $request['kode_produk'],
                'uuid_user' => Auth::user()->uuid
            ])->first();

            if(isset($check_wishlist)) {
                $check_wishlist->delete();
                return response()->json([
                    'status' => true,
                    'error' => false,
                    'message' => 'Yah, Produk Kamu Berhasil Dihapus Dari Wishlist.',
                    'detail' => []
                ]);
            }else {
                $wishlist = new Wishlist();
                $wishlist->kode_produk = $request['kode_produk'];
                $wishlist->uuid_user = Auth::user()->uuid;
                
                if($wishlist->save()) {
                    return response()->json([
                        'status' => true,
                        'error' => false,
                        'message' => 'Hore!, Produk Kamu Berhasil Ditambahkan Ke Wishlist.',
                        'detail' => $wishlist
                    ]);
                }
            }

            
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

}
