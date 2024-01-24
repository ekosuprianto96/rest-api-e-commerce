<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Message;
use App\Events\LiveChat;
use App\Events\TestLagi;
use App\Events\TestMessage;
use App\Http\Controllers\API\Handle\ErrorController;
use Illuminate\Http\Request;
use App\Jobs\SendInvoiceToko;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use RealRashid\SweetAlert\Facades\Alert;

class LoginController extends Controller
{
    public function index()
    {
        return view('admin.auth.login');
    }

    public function authenticate(Request $request)
    {
        $validate = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|max:16'
        ]);
        try {

            $check_account = User::where([
                'email' => $request->email
            ])->first();

            if (in_array($check_account->role, array('user', 'toko'))) {
                Alert::error('Gagal!', 'Login Failed');
                return redirect()->back();
            }

            if (Auth::attempt($validate)) {
                $request->session()->regenerate();
                return redirect()->intended('admin/dashboard/' . Auth::user()->uuid . '');
            }

            Alert::error('Gagal!', 'Login Failed');
            return redirect()->back();
        } catch (\Exception $err) {
            return ErrorController::getError($err);
        }
    }
}
