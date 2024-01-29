<?php

namespace App\Http\Controllers\Admin\Order;

use App\Models\DetailOrder;
use Illuminate\Http\Request;
use App\Models\NotifikasiAdmin;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\API\Handle\ErrorController;

class OrderController extends Controller
{
    public function daftar_order() {
        NotifikasiAdmin::where([
            'type' => 'daftar-order',
            'status_read' => 0
        ])->update([
            'status_read' => 1
        ]);
        
        return view('admin.order.index');
    }

    public function data_order(Request $request) {
        try {

            $where = '1=1';

    

            $transaksi = DetailOrder::whereRaw($where)
                                ->latest()->get();

            $data = DataTables::of($transaksi)
                    ->addColumn('no_order', function($list) {
                        return $list->no_order;
                    })
                    ->addColumn('nama_produk', function($list) {
                        return $list->produk->nm_produk;
                    })
                    ->addColumn('type_produk', function($list) {
                        return $list->type_produk;
                    })
                    ->addColumn('kategori', function($list) {
                        return $list->produk->kategori->nama_kategori;
                    })
                    ->addColumn('nama_pembeli', function($list) {
                        return $list->user->full_name;
                    })
                    ->addColumn('biaya', function($list) {
                        return 'Rp. '.number_format($list->total_biaya);
                    })
                    ->addColumn('status', function($list) {
                        $status = $list->status_order;
                        if($list->status_order == 'PENDING') {
                            $status = '<span class="badge badge-sm badge-warning">PENDING</span>';
                        }else if($list->status_order == 'SUCCESS') {
                            $status = '<span class="badge badge-sm badge-success">SUCCESS</span>';
                        }else if($list->status_order == 'CANCEL') {
                            $status = '<span class="badge badge-sm badge-danger">CANCEL</span>';
                        }else if($list->status_order == '0') {
                            $status = '<span class="badge badge-sm badge-danger">Belum Bayar</span>';
                        }elseif($list->status_order == 'PROCCESS') {
                            $status = '<span class="badge badge-sm badge-warning">PROCCESS</span>';
                        }

                        return $status;
                    })
                    ->addColumn('tanggal', function($list) {
                        return $list->created_at->format('Y-m-d');
                    })
                    ->addColumn('action', function($list) {
                        return '<div class="d-flex align-items-center justify-content-center" style="gap: 7px;">
                                    <a href="'.route('admin.order.detail-order', $list->no_order).'?id='.$list->id.'" class="btn btn-sm btn-primary text-nowrap" style="font-size: 0.8em"><i class="fa fa-eye"></i> Detail</a>
                                </div>';
                    })->rawColumns(['no_order', 'nama_produk', 'type_produk', 'kategori', 'nama_pembeli', 'biaya', 'status', 'tanggal', 'action'])
                    ->make(true);

            return $data;
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function detail_order(Request $request, $no_order) {
        $order = DetailOrder::where([
            'no_order' => $no_order,
            'id' => $request['id']
        ])->first();

        return view('admin.order.detail', compact('order'));
    }
}
