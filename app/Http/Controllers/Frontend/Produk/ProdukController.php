<?php

namespace App\Http\Controllers\Frontend\Produk;

use App\Models\Produk;
use App\Models\Wishlist;
use App\Models\FormProduk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\Handle\ErrorController;
use Illuminate\Support\Facades\Cache;

class ProdukController extends Controller
{
    public function show($slug)
    {
        try {
            $produk = Produk::with(['kategori', 'toko', 'form', 'images'])->where([
                'slug' => $slug,
                'an' => 1
            ])->first();

            if (Auth::user()) {
                $uuid_user = Auth::user()->uuid;
                $check_form = FormProduk::where([
                    'uuid_user' => $uuid_user,
                    'kode_produk' => $produk->kode_produk
                ])->get();
                if ($check_form) {
                    $produk->status_form = true;
                }
            } else {
                $produk->status_form = false;
            }


            $produk->total_produk_toko = $produk->toko->produk->count();
            $produk->total_terjual = $produk->order->count();
            $produk->total_terjual_toko = $produk->toko->order->count();
            $komisi = (float) ((intval($produk->komisi_referal) / 100) * $produk->getHargaFixed());
            $produk->komisi_referal = number_format($komisi, 0);
            $produk->wishlist = statusWishlist($produk);


            $produk->detail_harga = $produk->getHargaDiskon($produk);

            $produk['total_produk_toko'] = $produk->toko->produk->count();
            $produk->waktu_proses = $produk->waktuProses();
            $produk->harga = number_format($produk->harga, 2);

            $produkToko = Cache::tags('produkToko')->rememberForever('produkToko', function () use ($produk) {
                return $produk->toko->getProdukToko();
            });

            $produkSerupa = Produk::getProdukSerupa($produk);

            return view('frontend.produk.show', compact('produk', 'produkToko', 'produkSerupa'));
        } catch (\Exception $err) {
            return ErrorController::getError($err);
        }
    }
}
