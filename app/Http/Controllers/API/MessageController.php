<?php

namespace App\Http\Controllers\API;

use App\Models\Message;
use App\Events\LiveChat;
use App\Events\TestMessage;
use App\Jobs\SendMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\Handle\ErrorController;
use App\Models\DetailToko;

class MessageController extends Controller
{
    public function index() {

        // $message = Message::where('uuid_user', Auth::user()->uuid)->groupBy('kode_toko')->latest()->get();
        $pesan = Auth::user()->message->groupBy('kode_toko');

        $message = array();
        foreach($pesan as $key => $value) {
            $toko = DetailToko::where('kode_toko', $key)->first();
            $pesan = $toko->message;
            foreach($pesan as $ms) {
                $ms->tgl_pesan = $ms->created_at->format('d-m-y');
            }
            array_push($message, array(
                'nama_toko' => $toko->nama_toko,
                'kode_toko' => $toko->kode_toko,
                'message' => $pesan
            ));
        }
        return response()->json([
            'status' => true,
            'error' => false,
            'detail' => $message
        ], 200);
    }

    public function store(Request $request) {
        try {

            $message = new Message();
            $message->uuid_user = auth()->guard('api')->user()->uuid;
            $message->kode_toko = $request['kode_toko'];
            $message->message = $request['message'];

            if($message->save()) {

                event(new LiveChat(array(
                    'message' => $request['message'],
                    'kode_toko' => $request['kode_toko']
                )));

                return response()->json([
                    'status' => true,
                    'error' => false,
                    'detail' => $message
                ], 200);
            }
        }catch(\Exception $err) {
            return ErrorController::getError($err);
        }
    }
}
