<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\SendNotificationEmail;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\IorPay;
use Illuminate\Support\Facades\Hash;
use App\Notifications\RegisterNotification;

class RegisterController extends Controller
{
    public function store(Request $request) {
        $request->validate([
            'username' => 'required|string|min:6|max:12',
            'fullname' => 'required|string|min:3|max:50',
            'email' => 'required|email|min:6|max:32|unique:users',
            'password' => 'required|min:6|max:16',
            'no_hape' => 'required|min:8|max:14|unique:users',
            'alamat' => 'required|min:8|max:255' ,
            'tgl_lahir' => 'required|date_format:d-m-Y'
        ]);

        try {
            $request = $request->input();

            $user = new User();
            $user->uuid = Str::uuid(32);
            $user->username = str_replace(' ', '', strtolower($request['username']));
            $user->full_name = Str::title($request['fullname']);
            $user->alamat = Str::title($request['alamat']);
            $user->no_hape = $request['no_hape'];
            $user->email = $request['email'];
            $user->tgl_lahir = Carbon::parse($request['tgl_lahir'])->format('d-m-Y');
            $user->password = Hash::make($request['password']);
            if($user->save()) {
                IorPay::create([
                    'uuid_user' => $user->uuid,
                    'status_pay' => 1
                ]);

                SendNotificationEmail::dispatch($user);
                return response()->json([
                    'status' => true,
                    'error' => false,
                    'message' => 'Yeah, Selamat Kamu Berhasil Registrasi.',
                    'detail' => $user
                ], 200);
            }else {
                return response()->json([
                    'status' => true,
                    'error' => true,
                    'message' => 'Maaf!, Sepertinya Terjadi Kesalahan Pada Saat Registrasi, Silahkan Coba Lagi.',
                    'user' => []
                ], 409);
            }
        }catch(\Exception $err) {
            return response()->json([
                'status' => false,
                'error' => true,
                'message' => 'Maaf!, Sepertinya Kami Sedang Mengalami Ganguan System, Silahkan Coaba Lagi Beberapa Menit.',
                'detail_message' => $err->getMessage().'-'.$err->getLine(),
                'detail' => []
            ], 500);
        }
    }
}
