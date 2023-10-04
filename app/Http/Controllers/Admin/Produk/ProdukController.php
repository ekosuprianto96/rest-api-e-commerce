<?php

namespace App\Http\Controllers\Admin\Produk;

use App\Http\Controllers\API\Handle\ErrorController;
use App\Http\Controllers\Controller;
use App\Models\DetailToko;
use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class ProdukController extends Controller
{
    public function index() {
        $produk = Produk::with(['kategori', 'toko'])->where('status_confirm', 0)->paginate(20);

        return view('admin.produk.index', compact('produk'));
    }

    public function konfirmasi(Request $request, $kode_produk) {
        try {
            $produk = Produk::findOrFail($kode_produk);

            if($produk) {

                $produk->update([
                    'status_confirm' => 1
                ]);

                Alert::success('Sukses!', 'Berhasil Konfirmasi Produk Dari Toko : '.$produk->toko->nama_toko);
            }else {
                Alert::success('Gagal!', 'Gagal Konfirmasi Produk Dari Toko : '.$produk->toko->nama_toko.'Silahkan Coba Lagi.');
            }

            return redirect()->back();
        }catch(\Exception $err) {
            return ErrorController::getError($err);
        }
    }

    public function view_produk($kode_produk) {

        $produk = Produk::findOrFail($kode_produk);
        $produk->detail_harga = $produk->getHargaDiskon($produk);
        if(empty($produk)) {
            Alert::error('Maaf!', 'Produk Tidak Ditemukan, Atau Mungkin Sudah Dihapus.');

            return redirect()->back();
        }
        return view('admin.produk.view_produk', compact('produk'));
    }

    public function all_produk(Request $request) {

        $where = '1=1 ';
        if($request->nama_produk) {
            $where .= "and produk.nm_produk like '%".$request->nama_produk."%' ";
        }

        if($request->toko) {
            $where .= "and detail_toko.kode_toko like '%".$request->toko."%' ";
        }

        if($request->kategori) {
            $where .= "and kategori.kode_kategori like '%".$request->kategori."%' ";
        }

        $produk = Produk::selectRaw('produk.*, kategori.nama_kategori, detail_toko.nama_toko')
                        ->join('kategori', 'kategori.kode_kategori', 'produk.kode_kategori')
                        ->join('detail_toko', 'detail_toko.kode_toko', 'produk.kode_toko')->whereRaw($where)->paginate(20);
        $kategori = Kategori::all()->where('an', 1);
        $toko = DetailToko::all()->where('status_toko', 'APPROVED');
        return view('admin.produk.all_produk', compact('produk', 'kategori', 'toko'));
    }
}
