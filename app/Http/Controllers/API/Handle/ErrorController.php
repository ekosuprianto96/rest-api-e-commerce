<?php

namespace App\Http\Controllers\API\Handle;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;

class ErrorController extends Controller
{
    static function getResponseError($err, $http_code = 500) {
        return response()->json([
            'status' => false,
            'error' => true,
            'message' => 'Maaf!, Sepertinya Kami Sedang Mengalami Ganguan System, Silahkan Coba Beberapa Menit Lagi.',
            'detail' => $err->getMessage().'-'.$err->getLine()
        ], $http_code);
    }
    static function getError(\Exception $err) {
        Alert::error('Error!', 'Terjadi kesalahan system : '.$err->getMessage().'-'.$err->getLine());
            
        return redirect()->back();
    }
    public function message($message = null) {
        return with('error', $message ?? '');
    }

    static function redirectLogin($message = null) {

        Alert::error('Gagal Login!', $message ?? '');
        return redirect()->back()->with('error', $message ?? '');

    }

    static function redirectSuccessLogin(Request $request, $message) {
        Alert::success('Berhasil Login!', $message ?? '');
        $request->session()->regenerate();
        return redirect()->intended('/')->with('success', $message ?? '');
    }
}
