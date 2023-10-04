<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Handle\ErrorController;
use App\Models\User;
use Illuminate\Http\Request;
use App\Jobs\SendInvoiceToko;
use App\Jobs\SendNotificationEmail;
use App\Jobs\SendNotificationLogin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Notifications\LoginNotification;
use Illuminate\Support\Facades\Validator;
use App\Notifications\RegisterNotification;
use Illuminate\Support\Facades\File;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthenticateController extends Controller
{
    public function authenticate(Request $request) {
        $validator = $request->validate([
                            'email' => 'required|email|min:6|max:32',
                            'password' => 'required|min:6|max:16'
                        ]);
        
        try {

            $credentials = $request->only('email', 'password');
            if(!$token = auth()->guard('api')->attempt($credentials)) {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Email atau Password Anda salah'
                ], 401);
            }
            
            // if(Auth::attempt($validator)) {
            // }
            $user = User::where('uuid', auth()->guard('api')->user()->uuid)->first();      
            SendNotificationLogin::dispatch($user);
            return response()->json([
                'status' => true,
                'error' => false,
                'detail'    => [
                    'user' => auth()->guard('api')->user(),
                    'toko' => auth()->guard('api')->user()->toko,
                    'cart' => auth()->guard('api')->user()->cart,
                    'wishlist' => auth()->guard('api')->user()->wishlist
                ],    
                'token'   => $token   
            ], 200);
        }catch(\Exception $err) {
            return response()->json([
                'status' => false,
                'error'    => true,
                'detail' => $err->getMessage().'-'.$err->getLine(),
                'message' => 'Maaf!, Sepertinya Terjadi Kesalahan System, Silahkan Coaba Beberapa Menit Lagi.'  
            ], 500);
        }

    }

    public function logout(Request $request) {
        try {
            //remove token
            $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

            if($removeToken) {
                session()->regenerate(true);
                //return response JSON
                return response()->json([
                    'status' => true,
                    'error' => false,
                    'message' => 'Logout Berhasil!',  
                ], 200);
            }
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function update(Request $request) {
        $request->validate([
            'username' => 'required|string|min:6|max:12',
            'full_name' => 'required|string|min:3|max:50',
            'email' => 'required|email|min:6|max:32',
            'no_hape' => 'required|min:8|max:14',
            'alamat' => 'required|min:8|max:255',
            'tgl_lahir' => 'required'
        ]);

        try {

            $user = User::where('uuid', Auth::user()->uuid)->update($request->all());
            
            if($user) {

                if($request->hasFile('image')) {
                    $file = $request->file('image');
                    $fileName = Auth::user()->uuid . '.' . $file->getClientOriginalExtension();
                    $path = asset('assets/user/image/'.Auth::user()->uuid.'/').$fileName;
                    $file->move($path, $fileName);
                    $user->update([
                        'image' => $path
                    ]);
                }
                return response()->json([
                    'status' => true,
                    'error' => false,
                    'message' => 'Berhasil Update Akun',
                    'detail' => $user
                ], 200);
            }else {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Gagal Update Akun, Silahkan Periksa Kembali Form Anda.',
                    'detail' => []
                ], 400);
            }
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function upload_image(Request $request) {
        $request->validate([
            'image' => 'image|mimes:jpg,png,svg,jpeg,webp|max:30000'
        ]);

        try {
            $user = User::where('uuid', Auth::user()->uuid)->first();

            if($request->hasFile('image')) {
                if($user->image) {
                    File::delete(public_path('assets/users/image/'.Auth::user()->username).'/'.$user->image);
                }
                $file = $request->file('image');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $image = asset('assets/users/image/'.Auth::user()->username.'/'.$fileName);
                $file->move(public_path('assets/users/image/'.Auth::user()->username), $fileName);
                $user->image = $image;
                $user->save();
                return response()->json([
                    'status' => true,
                    'error' => false,
                    'message' => 'Berhasil Update Foto.',
                    'image' => $image
                ], 200);
            }
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
}
