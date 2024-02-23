<?php

namespace App\Http\Controllers\Frontend\Dashboard;

use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Produk;
use App\Models\Wishlist;
use App\Models\DetailOrder;
use Illuminate\Http\Request;
use App\Models\AksesDownload;
use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;
use App\Models\TransaksiKomisiReferal;
use App\Charts\Frontend\KomisiAfiliasi;
use App\Http\Controllers\API\Handle\ErrorController;

class DashboardController extends Controller
{
    public function index($username)
    {

        return view('frontend.dashboard.index')->render();
    }
    public function keranjang($username)
    {
        $carts = Cart::selectRaw('count(carts.kode_produk) as quantity, produk.*, carts.id as id_cart, detail_toko.kode_toko, detail_toko.nama_toko, detail_toko.image as image_toko, kategori.nama_kategori')
            ->join('produk', 'carts.kode_produk', 'produk.kode_produk')
            ->join('kategori', 'produk.kode_kategori', 'kategori.kode_kategori')
            ->join('detail_toko', 'produk.kode_toko', 'detail_toko.kode_toko')
            ->where('carts.uuid_user', Auth::user()->uuid)
            ->where('produk.an', 1)
            ->groupBy(['produk.kode_produk', 'id_cart'])
            ->get();
            
        $payments = PaymentMethod::where('status_payment', 1)->get();

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

        return view('frontend.dashboard.keranjang', compact('carts', 'payments'))->render();
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

    public function transaksi($username) {

        $transaksi = Order::with(['payment'])->where('uuid_user', Auth::user()->uuid)->latest()->get();

        foreach ($transaksi as $or) {
            $or->total_biaya = number_format($or->total_biaya, 0);
            $or->total_potongan = number_format($or->total_potongan, 0);
            $or->tanggal = $or->created_at->format('d M y');
        }

        $produkRekom = (new Produk())->getProdukRekomendasi();

        return view('frontend.dashboard.transaksi', compact('transaksi', 'produkRekom'));
    }

    public function linggaPay($username) {
        return view('frontend.dashboard.linggaPay');
    }

    public function komisiAffiliasi(KomisiAfiliasi $chart, $username) {
        
        $trx_pay = TransaksiKomisiReferal::with(['trx_iorpay', 'produk'])->where([
            'kode_pay' => Auth::user()->iorPay->kode_pay,
        ])->get();

        $komisi = [];
        $chart = $chart->build();
        
        if(@count($trx_pay) > 0) {
            foreach ($trx_pay as $trx) {
                $listKomisi = [
                    'nama_toko' => $trx->produk->toko->nama_toko,
                    'image' => $trx->produk->images[0]->url ?? config('app.logo'),
                    'nama_produk' => $trx->produk->nm_produk,
                    'detail_harga' => $trx->produk->getHargaDiskon(),
                    'total_komisi' => number_format($trx->trx_iorpay->total_fixed, 0),
                    'tanggal' => $trx->created_at->format('d M y'),
                    'status' => $trx->status_pembayaran
                ];
                $komisi['data'][] = $listKomisi;
                // $trx->
            }
        }else {
            $komisi['data'] = [];
        }

        $komisi['total']['total_komisi'] = number_format($trx_pay->sum('total_komisi'), 0);
        $komisi['total']['total_produk'] = $trx_pay->count();

        return view('frontend.dashboard.komisiAfiliasi', compact('chart', 'komisi'));
    }

    public function pesanan(Request $request, $username) {

        if(isset($request->no_order)) {

            if(empty($request->vx)) {
                return abort(404);
            }
            $pesanan = DetailOrder::with(['order', 'produk.images'])->where([
                'uuid_user' => Auth::user()->uuid,
                'no_order' => $request->no_order,
                'id' => $request->vx
            ])->first();

            if(empty($pesanan)) {
                return abort(404);
            }

            return view('frontend.dashboard.detail-pesanan', compact('pesanan'));
        }else {
            $pesanan = DetailOrder::with(['order', 'produk.images'])->where([
                'uuid_user' => Auth::user()->uuid
            ])->latest()->get();
    
            foreach ($pesanan as $order) {
                $produk = Produk::with(['images'])->where('kode_produk', $order->kode_produk)->first();
                $akses_download = AksesDownload::where([
                    'uuid_user' => Auth::user()->uuid,
                    'kode_produk' => $produk->kode_produk
                ])->first();
                $harga_produk = $produk->getHargaDiskon($produk);
                $order->nama_produk = $order->produk->nm_produk;
                $order->tanggal = $order->created_at->format('Y-m-d');
                $order->harga_produk = $harga_produk['harga_fixed'];
                $order->diskon = $harga_produk['harga_diskon'];
                $order->biaya = number_format($order->biaya, 0);
    
                if ($order->produk->type_produk == 'AUTO') {
                    if (isset($akses_download)) {
                        $order->url_download = route('download', ['token' => $akses_download->token, 'uuid' => $akses_download->uuid_user]);
                    } else {
                        $order->url_download = '';
                    }
                }
            }
    
            return view('frontend.dashboard.pesanan', compact('pesanan'));
        }
        
    }
    public function profile($username) {
        $user = User::find(Auth::user()->uuid);

        return view('frontend.dashboard.profile', compact('user'));
    }
    public function uploadImageProfile(Request $request) {
        $request->validate([
            'image' => 'image|mimes:jpg,png,svg,jpeg,webp|max:30000'
        ]);

        try {

            $user = User::where('uuid', Auth::user()->uuid)->first();

            if ($request->hasFile('image')) {
                if ($user->image) {
                    File::delete(public_path('assets/users/image/' . Auth::user()->username) . '/' . $user->image);
                }
                $file = $request->file('image');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $image = asset('assets/users/image/' . Auth::user()->username . '/' . $fileName);
                $file->move(public_path('assets/users/image/' . Auth::user()->username), $fileName);
                $user->image = $image;
                $user->save();

                return response()->json([
                    'status' => true,
                    'error' => false,
                    'message' => 'Berhasil Upload Foto.',
                    'image' => $image
                ], 200);
            }
        } catch (\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function destroyCart(Request $request) {
        try {   

            $cart = Cart::find($request->id);

            if(empty($cart)) {
                return response()->json([
                    'status' => true,
                    'error' => true,
                    'message' => 'Produk ini tidak ditemukan di keranjang.',
                    'detail' => null
                ]);
            }

            $cart->delete();

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Produk berhasil dihapus dari keranjang.',
                'detail' => 1
            ]);

        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
}
