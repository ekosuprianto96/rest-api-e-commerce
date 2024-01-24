<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Produk;
use App\Models\Wishlist;
use App\Models\FormProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $produk = Produk::with(['kategori', 'toko', 'form', 'images'])->latest()->where(['an' => 1, 'status_confirm' => 1])->take(50)->get();

        if (isset($request['kategori'])) {
            $produk = Produk::with(['kategori', 'toko', 'form', 'images'])->latest()->where(['an' => 1, 'status_confirm' => 1, 'kode_kategori' => $request['kategori']])->take(50)->get();
        }
        if (isset($request['keyword'])) {
            $produk = Produk::with(['kategori', 'toko', 'form', 'images'])->latest()->where(['an' => 1, 'status_confirm' => 1])
                ->where('nm_produk', 'like', "%{$request['keyword']}%")->take(50)->get();
        }
        foreach ($produk as $pr) {
            $pr->detail_harga = $pr->getHargaDiskon($pr);
        }

        if (auth()->guard('api')->user()) {
            foreach ($produk as $pr) {
                $wishlist = Wishlist::where([
                    'kode_produk' => $pr->kode_produk,
                    'uuid_user' => auth()->user()->uuid
                ])->first();

                if ($wishlist) {
                    if ($pr->kode_produk == $wishlist->kode_produk) {
                        $pr->wishlist = 1;
                    } else {
                        $pr->wishlist = 0;
                    }
                } else {
                    $pr->wishlist = 0;
                }
            }
        }

        $produkTerlaris = Cache::tags('produkTerlaris')->rememberForever('produkTerlaris', function () {
            return Produk::getProdukTerlaris();
        });

        foreach ($produkTerlaris as $terlaris) {
            $terlaris->detail_harga = $terlaris->getHargaDiskon($terlaris);
            if (Auth::check()) {
                $wishlist = Wishlist::where([
                    'kode_produk' => $terlaris->kode_produk,
                    'uuid_user' => auth()->user()->uuid
                ])->first();

                if ($wishlist) {
                    if ($terlaris->kode_produk == $wishlist->kode_produk) {
                        $terlaris->wishlist = 1;
                    } else {
                        $terlaris->wishlist = 0;
                    }
                } else {
                    $terlaris->wishlist = 0;
                }
            }
        }

        $semuaProduk = Produk::with(['kategori', 'toko', 'form', 'images'])->where(['an' => 1, 'status_confirm' => 1])->inRandomOrder()->take(50)->get();
        foreach ($semuaProduk as $produkAll) {
            $produkAll->detail_harga = $produkAll->getHargaDiskon($produkAll);
            if (Auth::check()) {
                $wishlist = Wishlist::where([
                    'kode_produk' => $produkAll->kode_produk,
                    'uuid_user' => auth()->user()->uuid
                ])->first();

                if ($wishlist) {
                    if ($produkAll->kode_produk == $wishlist->kode_produk) {
                        $produkAll->wishlist = 1;
                    } else {
                        $produkAll->wishlist = 0;
                    }
                } else {
                    $produkAll->wishlist = 0;
                }
            }
        }

        return view('frontend.home.index', compact('produk', 'produkTerlaris', 'semuaProduk'));
    }
}
