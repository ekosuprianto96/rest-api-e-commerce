<?php

namespace App\Http\Controllers\API;

use App\Models\Produk;
use App\Models\DetailOrder;
use Illuminate\Http\Request;
use App\Models\SettingWebsite;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\Handle\ErrorController;
use App\Models\AksesDownload;
use App\Models\WaktuProsesOrder;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{
    public function detail_pesanan(Request $request) {
        try {
            $order_detail = DetailOrder::with('file_pesanan')->where([
                'no_order' => $request['no_order'],
                'uuid_user' => $request['uuid_user'],
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
                'total_biaya' => $harga_produk['harga_fixed'],
                'image' => $order_detail->produk->image,
                'type_produk' => $order_detail->produk->type_produk,
                'form' => $order_detail->produk->form,
                'kode_toko' => $get_produk->kode_toko
            );

            $items['produk'] = $produk;

            $biaya_platform = SettingWebsite::first()->biaya_platform;
            $biaya_platform = (float) $biaya_platform / 100;
            $biaya_platform = (float) ($get_produk->getHargaFixed() * $biaya_platform);
            $total_pendapatan = (float) $get_produk->getHargaFixed() - $biaya_platform;

            $pesanan = null;
            $type_pesanan = null;
            if($order_detail->file_pesanan) {
                if($order_detail->file_pesanan->file != null) {
                    $pesanan = $order_detail->file_pesanan->file;
                    $type_pesanan = 'file';
                }else {
                    $pesanan = $order_detail->file_pesanan->text;
                    $type_pesanan = 'text';
                }
            }

            $order = array(
                'no_order' => $order_detail->no_order,
                'nama_toko' => $order_detail->toko->nama_toko,
                'biaya_platform' => number_format(SettingWebsite::first()->biaya_platform, 0),
                'total_pendapatan' => number_format($total_pendapatan, 0),
                'status_pembayaran' => $order_detail->order->status_order,
                'status_order' => $order_detail->status_order,
                'waktu_proses' => $order_detail->waktu_proses,
                'pesanan' => $pesanan,
                'type_pesanan' => $type_pesanan
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

    public function pesanan($uuid_user) {

        $detail_orders = DetailOrder::with(['order'])->where([
            'uuid_user' => $uuid_user
        ])->latest()->get();
        
        foreach($detail_orders as $order) {
            $produk = Produk::where('kode_produk', $order->kode_produk)->first();
            $akses_download = AksesDownload::where([
                'uuid_user' => Auth::user()->uuid,
                'kode_produk' => $produk->kode_produk
            ])->first();
            
            $harga_produk = $produk->getHargaDiskon($produk);
            $order->nama_produk = $order->produk->nm_produk;
            $order->tanggal = $order->created_at->format('Y-m-d');
            $order->harga_produk = $harga_produk['harga_fixed'];
            $order->diskon = $harga_produk['harga_diskon'];
            
            if($order->produk->type_produk == 'AUTO') {
                $order->url_download = route('download', ['token' => $akses_download->token, 'uuid' => $akses_download->uuid_user]);
            }
        }
        return response()->json([
            'status' => true,
            'error' => false,
            'message' => 'Berhasil get pesanan.',
            'detail' => $detail_orders
        ], 200);
    }

}