<?php

namespace App\Http\Controllers\Admin\Produk;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\DetailToko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\API\Handle\ErrorController;

class ProdukController extends Controller
{
    public function index() {
        $produk = Produk::with(['kategori', 'toko'])->where('status_confirm', 0)->paginate(20);

        return view('admin.produk.index', compact('produk'));
    }

    public function data_produk(Request $request) {
        try {

            $where = '1=1 ';
            if($request->nama_produk) {
                $where .= "and produk.nm_produk like '%".$request->nama_produk."%' ";
            }

            if($request->penjual) {
                $where .= "and detail_toko.kode_toko like '%".$request->penjual."%' ";
            }

            if($request->kategori) {
                $where .= "and kategori.kode_kategori like '%".$request->kategori."%' ";
            }

            $produk = Produk::selectRaw('produk.*, kategori.nama_kategori, detail_toko.nama_toko')
                            ->join('kategori', 'kategori.kode_kategori', 'produk.kode_kategori')
                            ->join('detail_toko', 'detail_toko.kode_toko', 'produk.kode_toko')->whereRaw($where)->get();

            $data = DataTables::of($produk)
                    ->addColumn('image', function($list) {
                        return '<img src="'.$list->image.'" width="50" alt="'.$list->nm_produk.'">';
                    })
                    ->addColumn('kode_produk', function($list) {
                        return $list->kode_produk;
                    })
                    ->addColumn('nama_produk', function($list) {
                        return $list->nm_produk;
                    })
                    ->addColumn('type_produk', function($list) {
                        return $list->type_produk;
                    })
                    ->addColumn('kategori', function($list) {
                        return $list->kategori->nama_kategori;
                    })
                    ->addColumn('harga', function($list) {
                        return 'Rp. '.number_format($list->harga, 0);
                    })
                    ->addColumn('nama_toko', function($list) {
                        return $list->toko->nama_toko;
                    })
                    ->addColumn('terjual', function($list) {
                        return $list->order->count();
                    })
                    ->addColumn('status', function($list) {
                        if($list->status_confirm) {
                            $status = '<span class="badge badge-sm badge-success">APPROVED</span>';
                        }else {
                            $status = '<span class="badge badge-sm badge-warning">PENDING</span>';
                        }
                        return $status;
                    })
                    ->addColumn('tanggal', function($list) {
                        return $list->created_at->format('Y-m-d');
                    })
                    ->addColumn('action', function($list) {
                        return '<div class="d-flex align-items-center justify-content-center" style="gap: 7px;">
                                    <a href="'.route('admin.produk.view-produk', $list->kode_produk).'" class="btn btn-sm btn-primary text-nowrap" style="font-size: 0.8em"><i class="fa fa-eye"></i> Detail</a>
                                </div>';
                    })->rawColumns(['image', 'kode_produk', 'nama_produk', 'type_produk', 'kategori', 'harga', 'nama_toko', 'terjual', 'status', 'tanggal', 'action'])
                    ->make(true);

            return $data;
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function update_deskripsi(Request $request, $kode_produk) {
        try {
            DB::beginTransaction();
            $produk = Produk::where('kode_produk', $kode_produk)->first();

            if(empty($produk)) {
                Alert::error('Error!', 'Data Produk Ditemukan.');
                return redirect()->back();
            }

            $produk->deskripsi = $request->deskripsi;
            $produk->save();

            Alert::success('Sukses', 'Deskripsi Berhasil Di Update.');
            DB::commit();
            return redirect()->back();
        }catch(\Exception $err) {
            return ErrorController::getError($err);
        }
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

    public function view_produk(Request $request, $kode_produk) {

        $url_back = ($request->url_back ? $request->url_back : null);
        $produk = Produk::findOrFail($kode_produk);
        $produk->detail_harga = $produk->getHargaDiskon($produk);
        if(empty($produk)) {
            Alert::error('Maaf!', 'Produk Tidak Ditemukan, Atau Mungkin Sudah Dihapus.');

            return redirect()->back();
        }
        return view('admin.produk.view_produk', compact('produk', 'url_back'));
    }

    public function all_produk(Request $request) {
        $kategori = Kategori::all()->where('an', 1);
        $toko = DetailToko::all()->where('status_toko', 'APPROVED');
        return view('admin.produk.all_produk', compact('kategori', 'toko'));
    }
}
