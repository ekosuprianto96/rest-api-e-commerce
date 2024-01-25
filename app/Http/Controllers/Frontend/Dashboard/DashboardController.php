<?php

namespace App\Http\Controllers\Frontend\Dashboard;

use App\Models\Cart;
use App\Models\Produk;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index($username)
    {
        return view('frontend.dashboard.index')->render();
    }
    public function keranjang($username)
    {
        $carts = Cart::selectRaw('count(carts.kode_produk) as quantity, produk.*, detail_toko.kode_toko, detail_toko.nama_toko, detail_toko.image as image_toko, kategori.nama_kategori')
            ->join('produk', 'carts.kode_produk', 'produk.kode_produk')
            ->join('kategori', 'produk.kode_kategori', 'kategori.kode_kategori')
            ->join('detail_toko', 'produk.kode_toko', 'detail_toko.kode_toko')
            ->where('carts.uuid_user', Auth::user()->uuid)
            ->where('produk.an', 1)
            ->groupBy('produk.kode_produk')
            ->get();

        foreach ($carts as $c) {
            $produk = Produk::with(['kategori', 'toko'])->where([
                'kode_produk' => $c->kode_produk,
                'an' => 1,
                'status_confirm' => 1
            ])->first();

            $c->harga = $produk->getHargaDiskon();
            $wishlist = Wishlist::where([
                'kode_produk' => $c->kode_produk,
                'uuid_user' => Auth::user()->uuid
            ])->first();

            if ($wishlist) {
                if ($c->kode_produk == $wishlist->kode_produk) {
                    $c->wishlist = 1;
                } else {
                    $c->wishlist = 0;
                }
            } else {
                $c->wishlist = 0;
            }
        }

        return view('frontend.dashboard.keranjang', compact('carts'))->render();
    }

    public function wishlist($username)
    {
        $wishlist = Wishlist::selectRaw('count(wishlists.kode_produk) as quantity, produk.*, detail_toko.kode_toko, detail_toko.nama_toko, detail_toko.image as image_toko, kategori.nama_kategori')
            ->join('produk', 'wishlists.kode_produk', 'produk.kode_produk')
            ->join('kategori', 'produk.kode_kategori', 'kategori.kode_kategori')
            ->join('detail_toko', 'produk.kode_toko', 'detail_toko.kode_toko')
            ->where('wishlists.uuid_user', Auth::user()->uuid)
            ->where('produk.an', 1)
            ->groupBy('produk.kode_produk')
            ->get();
        foreach ($wishlist as $c) {
            $produk = Produk::with(['kategori', 'toko'])->where([
                'kode_produk' => $c->kode_produk
            ])->first();
            $c->image_produk = $produk->images;
            $c->harga = $produk->getHargaDiskon();
            $c->form = $produk->form;
        }

        return view('frontend.dashboard.wishlist', compact('wishlist'))->render();
    }
}
