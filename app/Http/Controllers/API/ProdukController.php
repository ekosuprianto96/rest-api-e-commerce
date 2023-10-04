<?php

namespace App\Http\Controllers\API;

use App\Models\Produk;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DetailOrder;
use App\Models\FormProduk;
use App\Models\ListFormProduk;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class ProdukController extends Controller
{
    public function show(Request $request) {
        try {
            if(isset($request['slug'])) {
                $produk = Produk::with(['kategori', 'toko', 'form'])->where([
                    'slug' => $request->slug,
                    'an' => 1
                ])->first();
                
                if(Auth::user()) {
                    $uuid_user = Auth::user()->uuid;
                    $check_form = FormProduk::where([
                                                'uuid_user' => $uuid_user,
                                                'kode_produk' => $produk->kode_produk
                                            ])->get();
                    if($check_form) {
                        $produk->status_form = true;
                    }
                }else {
                    $produk->status_form = false;
                }
                $produk->total_produk_toko = $produk->toko->produk->count();
                $produk->total_terjual = $produk->order->count();
                $produk->total_terjual_toko = $produk->toko->order->count();
                $produk->wishlist = 0;
                if(auth()->guard('api')->user()) {
                    $wishlist = Wishlist::where([
                        'kode_produk' => $produk->kode_produk,
                        'uuid_user' => auth()->guard('api')->user()->uuid
                    ])->first();

                    if($wishlist) {
                        if($produk->kode_produk == $wishlist->kode_produk) {
                            $produk->wishlist = 1;
                        }else {
                            $produk->wishlist = 0;
                        }
                    }else {
                        $produk->wishlist = 0;
                    }
                }
                
                $produk->detail_harga = $produk->getHargaDiskon($produk);

                $produk['total_produk_toko'] = $produk->toko->produk->count();
                $produk->harga = number_format($produk->harga, 2);
                if(empty($produk)) {
                    return response()->json([
                        'status' => false,
                        'error' => true,
                        'slug' => $request->slug,
                        'message' => 'Maaf!, Sepertinya Kamu Mencari Produk Yang Tidak Ada Atau Sudah Dihapus.',
                        'detail' => null
                    ], 404);
                }
            }else {
                $produk = Produk::with(['kategori', 'toko', 'form'])->latest()->where(['an' => 1, 'status_confirm' => 1])->take(50)->get();

                if(isset($request['kategori'])) {
                    $produk = collect($produk)->where('kode_kategori', $request['kategori']);
                }
                if(isset($request['keyword'])) {
                    $produk = Produk::with(['kategori', 'toko', 'form'])->latest()->where(['an' => 1, 'status_confirm' => 1])
                                    ->where('nm_produk', 'like', "%{$request['keyword']}%")->take(50)->get();
                }
                foreach($produk as $pr) {
                    $pr->detail_harga = $pr->getHargaDiskon($pr);
                }

                if(auth()->guard('api')->user()) {
                    foreach($produk as $pr) {
                        $wishlist = Wishlist::where([
                            'kode_produk' => $pr->kode_produk,
                            'uuid_user' => auth()->guard('api')->user()->uuid
                        ])->first();

                        if($wishlist) {
                            if($pr->kode_produk == $wishlist->kode_produk) {
                                $pr->wishlist = 1;
                            }else {
                                $pr->wishlist = 0;
                            }
                        }else {
                            $pr->wishlist = 0;
                        }
                    }
                }
            }

            return response()->json([
                'status' => true,
                'error' => false,
                'slug' => $request->slug,
                'message' => 'Berhasil get produk',
                'detail' => $produk
            ], 200);
        }catch(\Exception $err) {
            return response()->json([
                'status' => false,
                'error' => true,
                'message' => 'Maaf!, Sepertinya Kami Sedang Mengalami Ganguan System, Silahkan Coba Beberapa Menit Lagi.',
                'detail' => $err->getMessage().'-'.$err->getLine()
            ], 500);
        }
    }
    public function store(Request $request) {
        $request->validate([
            'nama_produk' => 'required|string|min:6|max:100',
            'kategori' => 'required',
            'deskripsi_produk' => 'required',
            'file' => ($request->hasFile('file') ? 'mimes:txt,pdf,zip|max:2000000' : ''),
            'image' => 'mimes:png,jpeg,jpg,svg,webp|max:2000000',
            'list_form' => (isset($request['list_form']) ? 'required' : '')
        ]);

        try {
            // $request = $request->input();
            $produk = new Produk();
            $produk->nm_produk = Str::title($request['nama_produk']);
            $produk->slug = Str::slug($produk->nm_produk);
            $produk->kode_toko = Auth::user()->toko->kode_toko;
            $produk->kode_kategori = $request['kategori'];
            $produk->deskripsi = $request['deskripsi_produk'];
            $produk->harga = $request['harga'];
            $produk->potongan_harga = (isset($request['potongan_harga']) ? $request['potongan_harga'] : 0);
            $produk->potongan_persen = (isset($request['potongan_persen']) ? $request['potongan_persen'] : 0);
            $produk->image = 'no-image.jpg';
            $produk->link_referal = 'https://iorsale.com';

            if($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('file_produk/'.Auth::user()->toko->kode_toko, $fileName);
                $produk->type_produk = 'AUTO';
                $produk->file_name = $fileName;
            }else {
                $produk->type_produk = 'MANUAL';
            }
            if($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $path = asset('produk/image/'.Auth::user()->toko->kode_toko.'/'.$fileName);
                $file->move(public_path('produk/image/'.Auth::user()->toko->kode_toko), $fileName);
                $produk->image = $path;
            }
            if($produk->save()) {
                if($request->input('list_form')) {
                    $form_list = json_decode($request->input('list_form'));
                    foreach($form_list as $form) {
                        $list_form = new ListFormProduk();
                        $list_form->kode_produk = $produk->kode_produk;
                        $list_form->label = $form->name;
                        $list_form->type = $form->type;
                        $list_form->save();
                    }
                }
                return response()->json([
                    'status' => true,
                    'error' => false,
                    'message' => 'Asik, Produk Mu Telah Berhasil Di Upload, Silahkan Tunggu Konfirmasi Dari Admin Ya, Maksimal 2 x 24 Jam.',
                    'detail' => $produk,
                    'list' => $request['list_form']
                ], 200);
            }else {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Maaf!, Produk Kamu Gagal Diupload, Silahkan Coba Lagi.',
                    'detail' => null
                ], 422);
            }
        }catch(\Exception $err) {
            return response()->json([
                'status' => false,
                'error' => true,
                'message' => 'Maaf!, Sepertinya Kami Sedang Mengalami Ganguan System, Silahkan Coba Beberapa Menit Lagi.',
                'detail' => $err->getMessage().'-'.$err->getLine()
            ], 500);
        }
    }
}
