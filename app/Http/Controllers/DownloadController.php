<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AksesDownload;
use Illuminate\Support\Facades\Auth;

class DownloadController extends Controller
{
    public function download($token, $uuid) {

        $check_token = AksesDownload::where([
            'token' => $token,
            'uuid_user' => $uuid
        ])->first();
            // dd($check_token);
        if(isset($check_token)) {
            return response()->download($check_token->url_file);
        }else {
            return response()->json([
                'status' => false,
                'error' => true,
                'message' => 'Not Found'
            ], 404);
        }
    }
}
