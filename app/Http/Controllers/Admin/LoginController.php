<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Message;
use App\Events\LiveChat;
use App\Events\TestLagi;
use App\Events\TestMessage;
use Illuminate\Http\Request;
use App\Jobs\SendInvoiceToko;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class LoginController extends Controller
{
    public function index() {
        return view('admin.auth.login');
    }

    public function authenticate(Request $request) {
        $validate = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|max:16'
        ]);
        if (Auth::attempt($validate)) {
            $request->session()->regenerate();
            return redirect()->intended('admin/dashboard');
        }

        Alert::error('Gagal!', 'Login Failed');
        return redirect()->back();

    }
}
