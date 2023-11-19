<?php

namespace App\Http\Controllers;

use App\Models\User;
use Midtrans\Config;
use App\Models\Order;
use App\Helper\Helper;
use App\Models\Produk;
use App\Models\DetailToko;
use App\Models\DetailOrder;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ListFormProduk;
use App\Models\SettingGateway;
use App\Models\SettingWebsite;
use App\Models\WaktuProsesOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\API\Handle\ErrorController;
use App\Models\Notification;
use App\Models\PesananProduk;
use App\Models\Wishlist;

class DetailTokoController extends Controller
{
    public $settings_gateway;
    public $settings_web;
    public function __construct()
    {
        $this->settings_gateway = SettingGateway::first();
        $this->settings_web = SettingWebsite::first();
        Config::$serverKey = $this->settings_gateway->server_key;
        Config::$clientKey = $this->settings_gateway->client_key;
        Config::$isProduction = $this->settings_gateway->is_production;
        Config::$is3ds = $this->settings_gateway->is_3ds;
    }
    
    public function profile(Request $request) {
        try {

            $toko = DetailToko::with(['produk'])->where([
                'kode_toko' => $request['kode_toko']
            ])->first();
            
            if(empty($toko)) {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Maaf, Toko Tidak Ditemukan',
                    'detail' => []
                ], 404);
            }

            $toko->total_produk_terjual = $toko->order->count();
            foreach($toko->produk as $produk) {
                $wishlist = Wishlist::where([
                    'kode_produk' => $produk->kode_produk,
                    'uuid_user' => Auth::user()->uuid
                ])->first();

                $produk->nama_kategori = $produk->kategori->nama_kategori;
                $produk->detail_harga = $produk->getHargaDiskon();

                if(isset($wishlist)) {
                    $produk->wishlist = 1;
                }else {
                    $produk->wishlist = 0;
                }
            }
            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil get toko',
                'detail' => $toko
            ], 200);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
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

        $detail_orders = DetailOrder::with(['order'])->where([
            'kode_toko' => $kode_toko
        ])->latest()->get();
        
    
        foreach($detail_orders as $order) {
            $produk = Produk::where('kode_produk', $order->kode_produk)->first();

            $notif_order = Notification::where([
                'type' => 'order_toko',
                'to' => $produk->toko->user->uuid
            ])->first();

            if(isset($notif_order)) {
                $notif_order->delete();
            }

            $harga_produk = $produk->getHargaDiskon($produk);
            $order->nama_pembeli = $order->user->full_name;
            $order->tanggal = $order->created_at->format('Y-m-d');
            $order->total_biaya = $harga_produk['harga_fixed'];
            $order->total_potongan = $harga_produk['harga_diskon'];

            if($order->created_at->format('Y-m-d') == now()->format('Y-m-d')) {
                $order->status_new_order = 1;
            }else {
                $order->status_new_order = 0;
            }
        }
        return response()->json([
            'status' => true,
            'error' => false,
            'message' => 'Berhasil get order toko.',
            'detail' => $detail_orders
        ], 200);
    }

    public function produk(Request $request) {
        try {
            $produk_toko = Produk::with(['kategori', 'form'])->where([
                'kode_toko' => $request['kode_toko'],
                'an' => 1
            ])->latest()->get();

            foreach($produk_toko as $produk) {
                $produk->total_produk_toko = $produk->toko->produk->count();
                $produk->total_terjual = $produk->order->count();
                $produk->total_terjual_toko = $produk->toko->order->count();
                $produk->harga = $produk->getHargaDiskon($produk);
            }

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil get produk toko',
                'detail' => $produk_toko
            ], 200);

        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function destroy_produk(Request $request) {
        try {
            $produk_toko = Produk::where([
                'kode_produk' => $request['kode_produk'],
                'kode_toko' => $request['kode_toko']
            ])->first();

            if(isset($produk_toko)) {
                $produk_toko->an = 0;
                $produk_toko->save();

                return response()->json([
                    'status' => true,
                    'error' => false,
                    'message' => 'Berhasil Hapus Produk',
                    'detail' => 1
                ], 200);
            }

            return response()->json([
                'status' => false,
                'error' => false,
                'message' => 'Not Found',
                'detail' => 1
            ], 404);

        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function edit(Request $request) {
        try {
            $produk_toko = Produk::with(['kategori', 'form'])->where([
                'kode_produk' => $request['kode_produk'],
                'kode_toko' => $request['kode_toko']
            ])->first();
            
            if(empty($produk_toko)) {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Not Found',
                    'detail' => []
                ], 404);
            }

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil get produk',
                'detail' => $produk_toko
            ], 200);

        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function produk_update(Request $request) {

        $request->validate([
            'nama_produk' => 'required|string|min:6|max:100',
            'kategori' => 'required',
            'deskripsi_produk' => 'required',
            'harga' => 'required|numeric|min:1000',
            'total_komisi' => ($request['status_referal'] > 0 ? 'required|min:1000|max:'.$request['harga'].'|numeric' : ''),
            'file' => ($request->hasFile('file') ? 'mimes:txt,pdf,zip|max:2000000' : ''),
            'list_form' => (isset($request['list_form']) ? 'required' : '')
        ]);

        try {
            $produk_toko = Produk::where([
                                'kode_produk' => $request['kode_produk'],
                                'kode_toko' => $request['kode_toko']
                            ])->first();

            if(empty($produk_toko)) {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Not Found'
                ], 404);
            }

            $produk_toko->nm_produk = Str::title($request['nama_produk']);
            $produk_toko->slug = Str::slug($produk_toko->nm_produk);
            $produk_toko->kode_toko = Auth::user()->toko->kode_toko;
            $produk_toko->kode_kategori = $request['kategori'];
            $produk_toko->deskripsi = $request['deskripsi_produk'];
            $produk_toko->harga = $request['harga'];
            $produk_toko->potongan_harga = (isset($request['potongan_harga']) ? $request['potongan_harga'] : 0);
            $produk_toko->potongan_persen = (isset($request['potongan_persen']) ? $request['potongan_persen'] : 0);
            $produk_toko->link_referal = 'https://iorsale.com';

            if($request['status_referal'] > 0) {
                $produk_toko->status_referal = 1;
                $produk_toko->komisi_referal = $request['total_komisi'];
            }
            // if($request->hasFile('file')) {
            //     $file = $request->file('file');
            //     $fileName = time() . '.' . $file->getClientOriginalExtension();
            //     $file->storeAs('file_produk/'.Auth::user()->toko->kode_toko, $fileName);
            //     $produk_toko->type_produk = 'AUTO';
            //     $produk_toko->file_name = $fileName;
            // }else {
            //     $produk_toko->type_produk = 'MANUAL';
            // }
            
            if($request->hasFile('image')) {
                $request->validate([
                    'image' => 'mimes:png,jpeg,jpg,svg,webp|max:2000000'
                ]);

                if($produk_toko->image != null) {
                    File::delete(public_path('produk/image/'.Auth::user()->toko->kode_toko).$produk_toko->image);
                }
                $file = $request->file('image');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $path = asset('produk/image/'.Auth::user()->toko->kode_toko.'/'.$fileName);
                $file->move(public_path('produk/image/'.Auth::user()->toko->kode_toko), $fileName);
                $produk_toko->image = $path;
            }

            $produk_toko->save();
            // if($produk_toko->save()) {
            //     if($request->input('list_form')) {
            //         $form_list = json_decode($request->input('list_form'));
            //         foreach($form_list as $form) {
            //             $list_form = new ListFormProduk();
            //             $list_form->kode_produk = $produk_toko->kode_produk;
            //             $list_form->label = $form->name;
            //             $list_form->type = $form->type;
            //             $list_form->save();
            //         }
            //     }
            // }else {
            //     return response()->json([
            //         'status' => false,
            //         'error' => true,
            //         'message' => 'Maaf!, Produk Kamu Gagal Diupload, Silahkan Coba Lagi.',
            //         'detail' => null
            //     ], 422);
            // }

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil update produk'
            ], 200);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
    public function message() {
        $pesan = Auth::user()->toko->message->groupBy('uuid_user');

        $message = array();
        foreach($pesan as $key => $value) {
            $user = User::where('uuid', $key)->first();
            $pesan = $user->message->where('kode_toko', Auth::user()->toko->kode_toko);
            foreach($pesan as $ms) {
                $ms->tgl_pesan = $ms->created_at->format('d-m-y');
            }
            array_push($message, array(
                'nama_user' => $user->full_name,
                'uuid_user' => $user->uuid,
                'message' => $pesan
            ));
        }
        return response()->json([
            'status' => true,
            'error' => false,
            'detail' => $message
        ], 200);
    }

    public function detail_order(Request $request) {
        try {
            $order_detail = DetailOrder::where([
                'no_order' => $request['no_order'],
                'kode_toko' => $request['kode_toko'],
                'id' => $request['id']
            ])->first();
            
            $get_produk = Produk::where('kode_produk', $order_detail->kode_produk)->first();
            $harga_produk = $get_produk->getHargaDiskon($get_produk);

            $items = array();

            $produk = array(
                'nama_produk' => $order_detail->produk->nm_produk,
                'kategori' => $order_detail->produk->kategori->nama_kategori,
                'diskon' => ($get_produk->potongan_persen > 0 ? $get_produk->potongan_persen : ($get_produk->potongan_harga > 0 ? $get_produk->potongan_harga : 0)),
                'harga_diskon' => $harga_produk['harga_diskon'],
                'harga_real' => $harga_produk['harga_real'],
                'komisi_referal' => $order_detail->status_referal ? number_format($order_detail->produk->komisi_referal, 0) : 0,
                'image' => $order_detail->produk->image,
                'type_produk' => $order_detail->produk->type_produk,
                'status_referal' => $order_detail->produk->status_referal,
                'form' => $order_detail->produk->form
            );

            $items['produk'] = $produk;

            $biaya_platform = SettingWebsite::first()->biaya_platform;
            $biaya_platform = (float) $biaya_platform / 100;
            $biaya_platform = (float) ($get_produk->getHargaFixed() * $biaya_platform);
            $total_pendapatan = (float) $get_produk->getHargaFixed() - $biaya_platform;
            $status_new_order = 0;

            if($order_detail->created_at->format('Y-m-d') == now()->format('Y-m-d')) {
                $status_new_order = 1;
            }
            $order = array(
                'no_order' => $order_detail->no_order,
                'nama_pembeli' => $order_detail->user->full_name,
                'biaya_platform' => number_format(SettingWebsite::first()->biaya_platform, 0),
                'total_pendapatan' => number_format($total_pendapatan, 0),
                'status_pembayaran' => $order_detail->order->status_order,
                'status_order' => $order_detail->status_order,
                'waktu_proses' => $order_detail->waktu_proses,
                'status_new_order' => $status_new_order
            );

            $items['order'] = $order;

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil get detail order',
                'detail' => $items
            ], 200);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function upload_file(Request $request) {
        try {

            $order = DetailOrder::with(['user'])->where([
                'no_order' => $request['no_order'],
                'id' => $request['id']
            ])->first();

            if($request->hasFile('file')) {
                $file = $request->file('file');
                $ext = $file->getClientOriginalExtension();
                $newname = date('Ymd').rand(1000, 999).$order->uuid_user.'.'.$ext;
                $file->move(public_path('assets/users/'.$order->user->username), $newname);

                $param = [
                    'no_order' => $order->no_order,
                    'uuid_user' => $order->uuid_user,
                    'kode_toko' => $order->kode_toko,
                    'kode_produk' => $order->kode_produk,
                    'file' => asset('assets/users/'.$order->user->username.'/'.$newname)
                ];

                $status = PesananProduk::create($param);

                if($status) {
                    $order->status_order = 'SUCCESS';
                    $order->save();
                }
                return response()->json([
                    'status' => true,
                    'error' => false,
                    'message' => 'Berhasil mengirim file.',
                ], 200);
            }

            return response()->json([
                'status' => false,
                'error' => true,
                'message' => 'Maaf, Harap Upload File Anda.',
            ], 201);

        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
    public function prosesOrder(Request $request) {
        try {
            DB::beginTransaction();
            $order_detail = DetailOrder::where([
                'kode_toko' => $request['kode_toko'],
                'no_order' => $request['no_order'],
                'id' => $request['id']
            ])->first();
            
            if($request['waktu_proses'] == '' || $request['catatan'] == '') {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Waktu prosess atau Catatan harus diisi.'
                ], 402);
            }

            if($order_detail->status_order == 'PENDING') {
                $order_detail->status_order = 'PROCCESS';
                $order_detail->save();
            }

            $param = array(
                'waktu_proses' => $request['waktu_proses'],
                'catatan' => $request['catatan'],
                'order_id' => $order_detail->id
            );

            $waktu_proses = WaktuProsesOrder::create($param);

            DB::commit();
            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Order Produk Manual Berhasil Di Update Dangan Satatus '.$order_detail->status_order.'.',
                'detail' => $order_detail->status_order
            ], 200);

        }catch(\Exception $err) {
            DB::rollBack();
            return ErrorController::getResponseError($err);
        }
    }

    public function notifikasi(Request $request) {
        try {
            $notifikasi = Notification::where([
                'to' => $request['uuid_user'],
                'status_read' => 0
            ])->get();
            
            $detail['pesan_toko'] = count(collect($notifikasi)->where('type', 'pesan_toko'));
            $detail['order_toko'] = count(collect($notifikasi)->where('type', 'order_toko'));
            
            return response()->json(
                [
                    'status' => true,
                    'error' => false,
                    'message' => 'Berhasil get notifikasi',
                    'detail' => $detail
                ]
                );
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
}
