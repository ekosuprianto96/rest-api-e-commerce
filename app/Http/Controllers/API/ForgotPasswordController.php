<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\KirimLinkResetPassword;
use App\Http\Controllers\API\Handle\ErrorController;
use App\Jobs\SendNotificationEmail;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    public function konfirmasiEmail(Request $request) {
        try {
            $user = User::where('remember_token', $request->token)->first();

            if(empty($user)) {
                return response()->json([
                    'status' => false,
                    'error'=> true,
                    'message' => 'Maaf, Data Pengguna Tidak Ditemukan',
                    'detail' => null
                ]);
            }

            $user->remember_token = null;
            $user->email_verified_at = now();
            $user->save();

            return response()->json([
                'status' => true,
                'error'=> false,
                'message' => 'Sekses!, Berhasil Mengkonfirmasi Email.',
                'detail' => 1
            ]);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
    public function resendingEmail(Request $request) {
        try {
            $user = User::where('email', $request->email)->first();

            if(empty($user)) {
                return response()->json([
                    'status' => false,
                    'error'=> true,
                    'message' => 'Maaf, Data Pengguna Dengan Email: '.$request->email.' Tidak Ditemukan',
                    'detail' => null
                ]);
            }

            $data['key'] = Str::random(32);
            $data['nama'] = $user->full_name;
            $user->remember_token = $data['key'];
            $user->save();
            
            SendNotificationEmail::dispatch($user, $data);

            return response()->json([
                'status' => true,
                'error'=> false,
                'message' => 'Sekses!, Email Konfrimasi Telah Dikirim Ke '.$request->email.'.',
                'detail' => 1
            ]);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function sendLinkResetPassword(Request $request) {
        $request->validate([
            'email' => 'required|string|email'
        ]);
        try {
            $user = User::where('email', $request->email)->first();

            if(empty($user)) {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Mohon maaf, Email belum terdaftar.',
                    'detail' => 0
                ]);
            }

            $data['token'] = Str::random(32);
            $data['nama'] = $user->full_name;
            $data['url'] = env('URL_WEBSITE').'/'.'reset-password?token='.$data['token'].'';
            $response = KirimLinkResetPassword::dispatch($user, $data);
            
            $user->remember_token = $data['token'];
            $user->save();
            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Link Berhasil Dikirim ke email: '.$request->email.', periksa email lalu silahkan klik link untuk reset password anda.',
                'detail' => $data['token']
            ]);

        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
    public function resetPassword(Request $request) {
        $request->validate([
            'password' => 'required',
            'cpassword' => 'required'
        ]);
        try {

            $user = User::where('remember_token', $request->token)->first();

            if(empty($user)) {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Mohon maaf, link sudah tidak berlaku.',
                    'detail' => 0
                ]);
            }

            $user->password = Hash::make($request->password);
            $user->remember_token = null;
            $user->save();
            
            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Password berhasil diupdate.',
                'detail' => 1
            ]);

        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
}
