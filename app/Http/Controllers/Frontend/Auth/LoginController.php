<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Models\User;
use App\Models\LogUser;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Jobs\SendNotificationLogin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\Handle\ErrorController;
use Illuminate\Support\Facades\Redis;

class LoginController extends Controller
{
    public function login() {
        return view('frontend.auth.login');
    }

    public function authenticated(Request $request) {
        $request->validate([
            'email' => 'required|email|min:8',
            'password' => 'required|min:6'
        ]);
        try {
            
            // if(Auth::attempt($validator)) {
            // }
            $user = User::where('email', $request->email)->first();
           
            if(isset($user)) {
                foreach($user->roles as $role) {
                    if(!in_array($role->name, array('user', 'toko'))) {
                        return ErrorController::redirectLogin('Mohon maaf, Email atau Password salah.');
                    }
                }
            }

            $credentials = $request->only('email', 'password');
            if (!Auth::attempt($credentials)) {
                return ErrorController::redirectLogin('Mohon maaf, Email atau Password salah.');
            }

            Auth::login($user);

            if (isset($user) && isset($user->email_verified_at) && $user->status_banned > 0) {
                SendNotificationLogin::dispatch($user);
            }

            LogUser::create([
                'uuid_user' => $user->uuid,
                'tgl_login' => Carbon::now()->format('Y-m-d')
            ]);
            
           return ErrorController::redirectSuccessLogin($request, 'Berhasil Login');
        }catch(\Exception $err) {
            return ErrorController::getError($err);
        }
    }

    public function logout(Request $request) {
        try {

            Auth::logout();
 
            $request->session()->invalidate();
        
            $request->session()->regenerateToken();
            return redirect(route('home'));
        }catch(\Exception $err) {
            return ErrorController::getError($err);
        }
    }
}
