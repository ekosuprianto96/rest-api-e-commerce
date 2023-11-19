<?php

namespace App\Http\Controllers\Admin\Toko;

use App\Models\DetailToko;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\API\Handle\ErrorController;
use Illuminate\Support\Facades\Session;

class TokoController extends Controller
{
    public function index(Request $request) {
        return view('admin.toko.index');
    }

    public function data_toko(Request $request) {
        try {

            $where = '1=1 ';
            if($request->nama_toko) {
                $where .= "and nama_toko like '%".$request->nama_toko."%' ";
            }
            // dd($request->all());
            if(isset($request->status_toko)) {
                $where .= "and status_toko = '".$request->status_toko."' ";
            }

            $toko = DetailToko::selectRaw('detail_toko.*')->whereRaw($where)->get();

            $data = DataTables::of($toko)
                    ->addColumn('image', function($list) {
                        return '<img src="'.$list->image.'" width="50" alt="'.$list->nama_toko.'">';
                    })
                    ->addColumn('nama_toko', function($list) {
                        return $list->nama_toko;
                    })
                    ->addColumn('nama_pemilik', function($list) {
                        return $list->user->full_name;
                    })
                    ->addColumn('no_hape', function($list) {
                        return $list->user->no_hape;
                    })
                    ->addColumn('alamat', function($list) {
                        return $list->alamat_toko;
                    })
                    ->addColumn('status', function($list) {

                        if($list->status_toko == 'PENDING') {
                            $status = '<span class="badge badge-sm badge-warning">PENDING</span>';
                        }else if($list->status_toko == 'APPROVED') {
                            $status = '<span class="badge badge-sm badge-success">APPROVED</span>';
                        }

                        return $status;
                    })
                    ->addColumn('tanggal', function($list) {
                        return $list->created_at->format('Y-m-d');
                    })
                    ->addColumn('action', function($list) {
                        return '<div class="d-flex align-items-center justify-content-center" style="gap: 7px;">
                                    <a href="'.route('admin.toko.view', $list->kode_toko).'" class="btn btn-sm btn-primary text-nowrap" style="font-size: 0.8em"><i class="fa fa-eye"></i> Detail</a>
                                </div>';
                    })->rawColumns(['image', 'nama_toko', 'nama_pemilik', 'no_hape', 'alamat', 'status', 'tanggal', 'action'])
                    ->make(true);

            return $data;
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function data_konfirmasi(Request $request) {
        try {

            $toko = DetailToko::where('status_toko', 'PENDING')->latest()->get();

            $data = DataTables::of($toko)
                    ->addColumn('nama_toko', function($list) {
                        return $list->nama_toko;
                    })
                    ->addColumn('nama_pemilik', function($list) {
                        return $list->user->full_name;
                    })
                    ->addColumn('no_hape', function($list) {
                        return $list->user->no_hape;
                    })
                    ->addColumn('alamat', function($list) {
                        return $list->alamat_toko;
                    })
                    ->addColumn('status', function($list) {

                        if($list->status_toko == 'PENDING') {
                            $status = '<span class="badge badge-sm badge-warning">PENDING</span>';
                        }else if($list->status_toko == 'APPROVED') {
                            $status = '<span class="badge badge-sm badge-success">APPROVED</span>';
                        }

                        return $status;
                    })
                    ->addColumn('tanggal', function($list) {
                        return $list->created_at->format('Y-m-d');
                    })
                    ->addColumn('action', function($list) {
                        return '<div class="d-flex align-items-center justify-content-center" style="gap: 7px;">
                                    <a href="'.route('admin.toko.view', $list->kode_toko).'" class="btn btn-sm btn-primary text-nowrap" style="font-size: 0.8em"><i class="fa fa-eye"></i> Detail</a>
                                    <a href="javascript:void(0)" onclick="konfirmasi_toko('."'".$list->kode_toko."'".')" class="btn btn-sm btn-success text-nowrap" style="font-size: 0.8em"><i class="fa fa-eye"></i> Konfirmasi</a>
                                </div>';
                    })->rawColumns(['nama_toko', 'nama_pemilik', 'no_hape', 'alamat', 'status', 'tanggal', 'action'])
                    ->make(true);

            return $data;
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
    public function konfirmasi() {

        return view('admin.toko.konfirmasi');
    }

    public function konfirmasi_toko(Request $request) {

        try {
            $toko = DetailToko::findOrFail($request['kode_toko']);

            if(empty($toko)) {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Maaf, Sepertinya Toko Tidak Ditemukan',
                    'detail' => []
                ]);
            }

            if($toko->status_toko == 'APPROVED') {

                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Maaf, Toko Sudah Terkonfirmasi.',
                    'detail' => []
                ]);
            }   

            $toko->status_toko = 'APPROVED';
            $toko->save();

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Konfirmasi Toko '.$toko->nama_toko.' Berhasil.',
                'detail' => $toko
            ]);

        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function reject(Request $request, $kode_toko) {

        try {
            $toko = DetailToko::findOrFail($kode_toko);

            if(empty($toko)) {

                Alert::warning('Tidak Di Temukan', 'Maaf, Sepertinya Toko Tidak Ditemukan');

                return redirect()->back();
            }

            if($toko->status_toko == 'REJECT') {

                Alert::error('Mohon Maaf', 'Maaf, Toko Sudah Direject.');

                return redirect()->back();
            }   

            if($request->batal) {
                $toko->status_toko = 'PENDING'; 
            }else {
                $toko->status_toko = 'REJECT';
            }
            $toko->save();

            Alert::success('Sukses!', 'Toko '.$toko->nama_toko.' Berhasil Direject.');

            return redirect()->back();

        }catch(\Exception $err) {
            return ErrorController::getError($err);
        }
    }

    public function batal_konfirmasi(Request $request, $kode_toko) {

        try {
            $toko = DetailToko::findOrFail($kode_toko);

            if(empty($toko)) {

                Alert::warning('Tidak Di Temukan', 'Maaf, Sepertinya Toko Tidak Ditemukan');

                return redirect()->back();
            }

            if($toko->status_toko == 'PENDING' || $toko->status_toko == 'REJECT') {

                Alert::error('Mohon Maaf', 'Maaf, Toko Belum Dikonfirmasi.');

                return redirect()->back();
            }   

            $toko->status_user = 0;
            $toko->save();

            Alert::success('Sukses!', 'Konfirmasi Toko '.$toko->nama_toko.' Berhasil Dibatalkan.');

            return redirect()->back();

        }catch(\Exception $err) {
            return ErrorController::getError($err);
        }
    }

    public function view_toko(Request $request, $kode_toko) {
        
        $toko = DetailToko::findOrFail($kode_toko);
        $url_back = null;

        if($request->by_produk) {
            $url_back = route('admin.produk.view-produk', $request->by_produk);
        }

        if(!isset($toko)) {
            Alert::warning('Tidak Ditemukan!', 'Maaf, Toko Tidak Ditemukan');

            return redirect()->back();
        }

        return view('admin.toko.view', compact('toko', 'url_back'));
    }
}
