<?php

namespace App\Http\Controllers\Admin\Transaksi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\API\Handle\ErrorController;
use App\Models\Order;
use App\Models\Produk;

class TransaksiController extends Controller
{
    public function index() {
        return view('admin.transaksi.index');
    }

    public function data_transaksi(Request $request) {
        try {

            $where = '1=1';

            if($request->no_order) {
                $where .= " and no_order like '%$request->no_order%'";
            }
            if($request->type_pembayaran) {
                $where .= " and type_payment = '$request->type_pembayaran'";
            }
            if($request->bank) {
                $where .= " and payment_method = '$request->bank'";
            }
            if(isset($request->status_order)) {
                $where .= " and status_order = '$request->status_order'";
            }
            if(isset($request->tanggal_mulai)) {
                $where .= " and DATE_FORMAT(created_at, '%Y-%m-%d') >= '$request->tanggal_mulai'";
            }
            if(isset($request->tanggal_akhir)) {
                $where .= " and DATE_FORMAT(created_at, '%Y-%m-%d') <= '$request->tanggal_akhir'";
            }

            $transaksi = Order::whereRaw($where)
                                ->latest()->get();

            $data = DataTables::of($transaksi)
                    ->addColumn('no_order', function($list) {
                        return $list->no_order;
                    })
                    ->addColumn('nama_pembeli', function($list) {
                        return $list->user->full_name;
                    })
                    ->addColumn('type_pembayaran', function($list) {
                        return $list->type_payment;
                    })
                    ->addColumn('quantity', function($list) {
                        return $list->quantity;
                    })
                    ->addColumn('biaya', function($list) {
                        return 'Rp. '.number_format($list->total_biaya);
                    })
                    ->addColumn('status', function($list) {

                        if($list->status_order == 'PENDING') {
                            $status = '<span class="badge badge-sm badge-warning">PENDING</span>';
                        }else if($list->status_order == 'SUCCESS') {
                            $status = '<span class="badge badge-sm badge-success">SUCCESS</span>';
                        }else if($list->status_order == 'CANCEL') {
                            $status = '<span class="badge badge-sm badge-danger">CANCEL</span>';
                        }else if($list->status_order == '0') {
                            $status = '<span class="badge badge-sm badge-danger">Belum Bayar</span>';
                        }

                        return $status;
                    })
                    ->addColumn('tanggal', function($list) {
                        return $list->created_at->format('Y-m-d');
                    })
                    ->addColumn('action', function($list) {
                        return '<div class="d-flex align-items-center justify-content-center" style="gap: 7px;">
                                    <a href="'.route('admin.transaksi.detail', $list->no_order).'" class="btn btn-sm btn-primary text-nowrap" style="font-size: 0.8em"><i class="fa fa-eye"></i> Detail</a>
                                </div>';
                    })->rawColumns(['nama_toko', 'nama_pemilik', 'no_hape', 'biaya', 'alamat', 'status', 'tanggal', 'action'])
                    ->make(true);

            return $data;
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function data_transaksi_produk(Request $request) {
        try {

            $transaksi = Order::where('no_order', $request->no_order)->first();
            $produk = $transaksi->detail;
            $data = DataTables::of($produk)
                    ->addColumn('image', function($list) {
                        return '<img width="60" src="'.$list->produk->image.'">';
                    })
                    ->addColumn('nama_produk', function($list) {
                        return $list->produk->nm_produk;
                    })
                    ->addColumn('kategori', function($list) {
                        return $list->produk->kategori->nama_kategori;
                    })
                    ->addColumn('type_produk', function($list) {
                        return $list->produk->type_produk;
                    })
                    ->addColumn('harga_real', function($list) {
                        return number_format($list->produk->harga, 0);
                    })
                    ->addColumn('harga_fixed', function($list) {
                        $harga_fixed = Produk::where('kode_produk', $list->kode_produk)->first();
                        $harga_fixed = $harga_fixed->getHargaFixed();
                        return 'Rp. '.number_format($harga_fixed, 0);
                    })
                    ->addColumn('total_diskon', function($list) {
                        $harga_fixed = Produk::where('kode_produk', $list->kode_produk)->first();
                        $harga_fixed = $harga_fixed->getHargaDiskon();
                        return 'Rp. '.$harga_fixed['harga_diskon'];
                    })
                    ->addColumn('action', function($list) {
                        return '<div class="d-flex align-items-center justify-content-center" style="gap: 7px;">
                                    <a href="'.route('admin.produk.view-produk', $list->kode_produk).'?url_back='.route('admin.transaksi.detail', $list->no_order).'" class="btn btn-sm btn-primary text-nowrap" style="font-size: 0.8em"><i class="fa fa-eye"></i> View</a>
                                </div>';
                    })->rawColumns(['image', 'nama_produk', 'kategori', 'type_produk', 'harga_real', 'harga_fixed', 'total_diskon', 'action'])
                    ->make(true);

            return $data;
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function detail($no_order) {

        $order = Order::where('no_order', $no_order)->first();
        return view('admin.transaksi.detail', compact('order'));
    }
}
