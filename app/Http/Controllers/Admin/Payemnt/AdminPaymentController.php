<?php

namespace App\Http\Controllers\Admin\Payemnt;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use function PHPSTORM_META\type;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\API\Handle\ErrorController;

class AdminPaymentController extends Controller
{
    public function daftar_payment() {
        return view('admin.payment.daftar-payment');
    }

    public function data_payment(Request $request) {
        try {
            $where = '1=1';

            $payment = PaymentMethod::whereRaw($where)->latest()->get();

            $data = DataTables::of($payment)
                    ->addColumn('image', function($list) {
                        return '<img src="'.$list->image.'" alt="'.$list->payment_name.'" width="50">';
                    })
                    ->addColumn('kode_pay', function($list) {
                        return $list->kode_payment;
                    })
                    ->addColumn('nama', function($list) {
                        return $list->payment_name;
                    })
                    ->addColumn('nama_pemilik', function($list) {
                        return $list->nama_pemilik;
                    })
                    ->addColumn('norek', function($list) {
                        return $list->no_rek;
                    })
                    ->addColumn('status', function($list) {
                        $status = $list->status_payment;
                        if($status) {
                            return '<span class="badge badge-sm badge-success">AKTIF</span>';
                        }else {
                            return '<span class="badge badge-sm badge-danger">NON AKTIF</span>';
                        }
                    })
                    ->addColumn('tanggal', function($list) {
                        return $list->created_at->format('Y-m-d');
                    })
                    ->addColumn('action', function($list) {
                        return '<div class="d-flex align-items-center justify-content-center" style="gap: 7px;">
                                    <a data-payment="'.$list->kode_payment.'" onClick="showFormEdit(event)" href="javascript:void(0)" class="btn btn-create-role btn-sm btn-success text-nowrap" style="font-size: 0.8em"><i data-payment="'.$list->kode_payment.'" class="fa fa-edit"></i> Edit</a>
                                    <a data-payment="'.$list->kode_payment.'" onClick="deletedPayment(event)" href="javascript:void(0)" class="btn btn-create-role btn-sm btn-danger text-nowrap" style="font-size: 0.8em"><i data-payment="'.$list->kode_payment.'" class="fa fa-trash"></i> Hapus</a>
                                </div>';
                    })->rawColumns(['image', 'kode_pay', 'nama', 'nama_pemilik', 'norek', 'status', 'tanggal', 'action'])
                    ->make(true);

            return $data;
        }catch(\Exception $err) {
            return ErrorController::getError($err);
        }
    }

    public function store(Request $request) {
        if($request->type == 'bank') {
            $request->validate([
                'nama_bank' => 'required',
                'nama_pemilik' => 'required',
                'norek' => 'required'
            ]);
        }elseif($request->type == 'wallet') {
            $request->validate([
                'nama_wallet' => 'required',
                'nama_pemilik_wallet' => 'required',
                'notelpon_wallet' => 'required'
            ]);
        }

        try {
            $payment = new PaymentMethod();
            
            if($request->type == 'bank') {
                $payment->nama_pemilik = $request->nama_pemilik;
                $payment->payment_name = $request->nama_bank;
                $payment->no_rek = $request->norek;
            }elseif($request->type == 'wallet') {
                $payment->nama_pemilik = $request->nama_pemilik_wallet;
                $payment->payment_name = $request->nama_wallet;
                $payment->no_rek = $request->notelpon_wallet;
            }
            $payment->type = $request->type;

            if(isset($request->status)) {
                $payment->status_payment = $request->status;
            }

            if($request->hasFile('icon')) {
                $file = $request->file('icon');
                $ext = $file->getClientOriginalExtension();
                $name = 'icon-'.$payment->payment_name.'-'.rand(1000, 9999).'.'.$ext;
                $file->move(public_path('assets/payment'), $name);
                $payment->image = asset('assets/payment/'.$name);
                $payment->image_name = $name;
            }

            if($payment->save()) {
                return response()->json([
                    'status' => true,
                    'error' => false,
                    'message' => 'Berhasil menambahkan payment',
                    'detail' => $payment
                ], 200);
            }

            return response()->json([
                'status' => false,
                'error' => true,
                'message' => 'Maaf, data gagal disimpan, silahkan coba kembali.',
                'detail' => 0
            ], 201);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function update(Request $request) {
        if($request->type == 'bank') {
            $request->validate([
                'nama_bank' => 'required',
                'nama_pemilik' => 'required',
                'norek' => 'required'
            ]);
        }elseif($request->type == 'wallet') {
            $request->validate([
                'nama_wallet' => 'required',
                'nama_pemilik_wallet' => 'required',
                'notelpon_wallet' => 'required'
            ]);
        }

        try {
            $payment = PaymentMethod::where([
                'kode_payment' => $request->kode_payment
            ])->first();
            
            
            if($payment->type == 'bank') {
                $payment->nama_pemilik = $request->nama_pemilik;
                $payment->payment_name = $request->nama_bank;
                $payment->no_rek = $request->norek;
            }elseif($payment->type == 'wallet') {
                $payment->nama_pemilik = $request->nama_pemilik_wallet;
                $payment->payment_name = $request->nama_wallet;
                $payment->no_rek = $request->notelpon_wallet;
            }

            if(isset($request->status)) {
                $payment->status_payment = $request->status;
            }

            if($request->hasFile('icon')) {
                File::delete(public_path('assets/payment/').$payment->image_name);
                $file = $request->file('icon');
                $ext = $file->getClientOriginalExtension();
                $name = 'icon-'.$payment->payment_name.'-'.rand(1000, 9999).'.'.$ext;
                $file->move(public_path('assets/payment'), $name);
                $payment->image = asset('assets/payment/'.$name);
                $payment->image_name = $name;
            }

            if($payment->save()) {
                return response()->json([
                    'status' => true,
                    'error' => false,
                    'message' => 'Berhasil update payment',
                    'detail' => $payment
                ], 200);
            }

            return response()->json([
                'status' => false,
                'error' => true,
                'message' => 'Maaf, data gagal disimpan, silahkan coba kembali.',
                'detail' => 0
            ], 201);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function edit(Request $request) {
        try {
            $payment = PaymentMethod::where([
                'kode_payment' => $request->kode_payment
            ])->first();

            if(empty($payment)) {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Data No Found.',
                    'detail' => null
                ], 404);
            }

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Data Payment Behasil Di Get.',
                'detail' => $payment
            ], 200);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function destroy(Request $request) {
        try {
            $payment = PaymentMethod::where([
                'kode_payment' => $request->kode_payment
            ])->first();

            if(empty($payment)) {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Data No Found.',
                    'detail' => null
                ], 404);
            }
            File::delete(public_path('assets/payment/').$payment->image_name);
            $payment->delete();

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Data payment berhasil di hapus.',
                'detail' => 1
            ], 200);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
}
