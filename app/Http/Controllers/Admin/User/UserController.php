<?php

namespace App\Http\Controllers\Admin\User;

use App\Models\User;
use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Models\NotifikasiAdmin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Controllers\API\Handle\ErrorController;

class UserController extends Controller
{
    public function index(Request $request) {

        $where = '1=1 ';
        if($request->nama_user) {
            $where .= "and full_name like '%".$request->nama_user."%'
             or email like '%".$request->nama_user."%' or 
             no_hape like '%".$request->nama_user."%' or
             alamat like '%".$request->nama_user."%' ";
        }
        // dd($request->all());
        if(isset($request->status)) {
            $where .= "and status_user = '".$request->status."' ";
        }

        $users = User::selectRaw('users.*')->whereRaw($where)->paginate(20);

        return view('admin.user.index', compact('users'));
    }

    public function konfirmasi() {

        $users = User::where('status_user', 0)->latest()->paginate(20);

        NotifikasiAdmin::where([
            'type' => 'konfirmasi-user',
            'status_read' => 0
        ])->update([
            'status_read' => 1
        ]);

        return view('admin.user.konfirmasi', compact('users'));
    }

    public function konfirmasi_user(Request $request, $uuid_user) {

        try {
            $user = User::findOrFail($uuid_user);

            if(empty($user)) {

                Alert::warning('Tidak Di Temukan', 'Maaf, Sepertinya User Tidak Ditemukan');

                return redirect()->back();
            }

            if($user->status_user == 1) {

                Alert::error('Mohon Maaf', 'Maaf, User Sudah Terkonfirmasi.');

                return redirect()->back();
            }   

            $user->status_user = 1;
            $user->save();

            Alert::success('Sukses!', 'User '.$user->full_name.' Berhasil Di Konfirmasi.');

            return redirect()->back();

        }catch(\Exception $err) {
            return ErrorController::getError($err);
        }
    }
    public function blockPengguna(Request $request) {

        try {
            $user = User::findOrFail($request->uuid);

            if(empty($user)) {

                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Mohon maaf, Pengguna tidak ditemukan/belum terdaftar.',
                    'detail' => null
                ]);
            }   

            if($user->status_banned == 1) {

                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Mohon maaf, Pengguna sudah dalam sataus blockir.',
                    'detail' => null
                ]);
            }   

            $user->status_banned = 1;
            $user->save();

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Pengguna '.$user->full_name.' Berhasil Diblock.',
                'detail' => 1
            ]);

        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function bukaBlock(Request $request) {

        try {
            $user = User::findOrFail($request->uuid);

            if(empty($user)) {

                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Mohon maaf, Pengguna tidak ditemukan/belum terdaftar.',
                    'detail' => null
                ]);
            }   

            if($user->status_banned == 0) {

                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Mohon maaf, Pengguna masih dalam status Aktif.',
                    'detail' => null
                ]);
            }     

            $user->status_banned = 0;
            $user->save();

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Pengguna '.$user->full_name.' Berhasil Diaktifkan Kembali.',
                'detail' => 1
            ]);

        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function view_user(Request $request, $uuid_user) {
        
        $user = User::findOrFail($uuid_user);
        $url_back = null;

        if($request->by_produk) {
            $url_back = route('admin.produk.view-produk', $request->by_produk);
        }

        if(!isset($user)) {
            Alert::warning('Tidak Ditemukan!', 'Maaf, User Tidak Ditemukan');

            return redirect()->back();
        }

        return view('admin.user.view', compact('user', 'url_back'));
    }

    public function daftarUser(Request $request) {
        $where = '1=1 ';
        if($request->nama_user) {
            $where .= "and full_name like '%".$request->nama_user."%'
             or email like '%".$request->nama_user."%' or 
             no_hape like '%".$request->nama_user."%' or
             alamat like '%".$request->nama_user."%' ";
        }
        // dd($request->all());
        if(isset($request->status)) {
            $where .= "and status_user = '".$request->status."' ";
        }

        $users = User::selectRaw('users.*')->whereRaw($where)->paginate(20);

        return view('admin.user.daftar-user', compact('users'));
    }

    public function logout(Request $request) {
        try {

            Auth::logout();
 
            $request->session()->invalidate();
        
            $request->session()->regenerateToken();
            return redirect('/');
        }catch(\Exception $err) {
            return ErrorController::getError($err);
        }
    }
}
