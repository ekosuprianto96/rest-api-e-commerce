<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Message;
use App\Events\LiveChat;
use App\Events\NotifikasiPesan;
use App\Jobs\SendMessage;
use App\Models\DetailToko;
use App\Events\TestMessage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\API\Handle\ErrorController;
use App\Models\MessageUser;
use App\Models\Notification;

class MessageController extends Controller
{
    public function open(Request $request) {
        try {
            if(Auth::user()->messages->count() > 0) {
                $message = Auth::user()->messages->where('to', $request['to'])[0];
                if(empty($message)) {
                    $msg = Message::create([
                        'id_room' => Str::uuid(),
                        'from' => Auth::user()->uuid,
                        'to' => $request['to'],
                        'value' => json_encode([])
                    ]);

                    MessageUser::create([
                        'uuid_user' => Auth::user()->uuid,
                        'message_id' => $msg->id_room
                    ]);
                    
                    if(Auth::user()->uuid != $request['to']) {
                        MessageUser::create([
                            'uuid_user' => $request['to'],
                            'message_id' => $msg->id_room
                        ]);
                    }
                }
            }else {
                $message = Message::create([
                    'id_room' => Str::uuid(),
                    'from' => Auth::user()->uuid,
                    'to' => $request['to'],
                    'value' => json_encode([])
                ]);

                MessageUser::create([
                    'uuid_user' => Auth::user()->uuid,
                    'message_id' => $message->id_room
                ]);

                if(Auth::user()->uuid != $request['to']) {
                    MessageUser::create([
                        'uuid_user' => $request['to'],
                        'message_id' => $message->id_room
                    ]);
                }
            }
            
            if($request['to'] == Auth::user()->uuid) {
                $get_toko = DetailToko::where('uuid_user', $message->to)->first();
                $message->nama_toko = $message->toUser->toko->nama_toko;
                $message->image = $message->toUser->toko->image;
                $message->uuid_user = $message->toUser->uuid;
            }else {
                $message->nama_toko = $message->toUser->toko->nama_toko;
                $message->image = $message->toUser->toko->image;
                $message->uuid_user = $message->toUser->uuid;
            }
            

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil get message',
                'detail' => ($request['to'] == Auth::user()->uuid ? $message : $message)
            ], 200);

        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
    // public function open_message_toko(Request $request) {
    //     try {
            
    //         $message = Message::with(['toko'])->where([
    //             'kode_toko' => $request['kode_toko'],
    //             'uuid_user' => $request['uuid_user']
    //         ])->first();
            
    //         if(empty($message)) {
    //             $message = Message::create([
    //                             'id_room' => Str::uuid(),
    //                             'kode_toko' => $request['kode_toko'],
    //                             'uuid_user' => $request['uuid_user'],
    //                             'value' => json_encode([])
    //                         ]);
    //         }

    //         $message->nama_user = $message->user->full_name;
    //         $message->uuid_user = $message->user->uuid_user;
    //         $message->image = $message->user->image;
            
    //         return response()->json([
    //             'status' => true,
    //             'error' => false,
    //             'message' => 'Berhasil get message',
    //             'detail' => $message
    //         ], 200);

    //     }catch(\Exception $err) {
    //         return ErrorController::getResponseError($err);
    //     }
    // }
    public function index(Request $request) {
        // $message = Message::where('uuid_user', Auth::user()->uuid)->groupBy('kode_toko')->latest()->get();
        $pesan = Auth::user()->messages;
        
        foreach($pesan as $key => $chat) {
            $value = json_decode($chat->value, true);
        
            if(Auth::user()->uuid == $chat->to) {
                $chat->nama_toko = $chat->fromUser->full_name;
                $chat->image = $chat->fromUser->image;
                $chat->to = $chat->fromUser->uuid;
                $chat->uuid_user = $chat->fromUser->uuid;
            }else {
                $chat->nama_toko = $chat->toUser->toko->nama_toko;
                $chat->image = $chat->toUser->toko->image;
                $chat->to = $chat->toUser->uuid;
                $chat->uuid_user = $chat->toUser->uuid;
            }

            $notif_order = Notification::where([
                'type' => 'pesan',
                'to' => $chat->to
            ])->get();

            if(isset($notif_order)) {
                foreach($notif_order as $notif) {
                    $notif->delete();
                }
            }
            // $get_toko = DetailToko::where('uuid_user', $chat->to)->first();
            // $chat->nama_toko = $get_toko->nama_toko;
            // $chat->to = $get_toko->uuid_user;
            // $chat->image = $get_toko->image;


            if(count($value) <= 0) {
                $chat->last_message = 0;
            }else {
                $chat->last_message = $value[count($value)-1];
            }
        }
        
        return response()->json([
            'status' => true,
            'error' => false,
            'detail' => $pesan
        ], 200);
    }
    // public function message_toko(Request $request) {
    //     // $message = Message::where('uuid_user', Auth::user()->uuid)->groupBy('kode_toko')->latest()->get();
    //     $pesan = Message::with(['user', 'toko'])->where('kode_toko', $request['kode_toko'])->get();

    //     foreach($pesan as $chat) {
    //         $value = json_decode($chat->value, true);
    //         $notif_order = Notification::where([
    //             'type' => 'pesan_toko',
    //             'to' => $chat->toko->user->uuid
    //         ])->get();

    //         if(isset($notif_order)) {
    //             foreach($notif_order as $notif) {
    //                 $notif->delete();
    //             }
    //         }

    //         if(count($value) <= 0) {
    //             $chat->last_message = 0;
    //         }else {
    //             $chat->last_message = $value[count($value)-1];
    //         }
    //     }
        
    //     return response()->json([
    //         'status' => true,
    //         'error' => false,
    //         'detail' => $pesan
    //     ], 200);
    // }

    public function store(Request $request) {
        try {

            $message = Message::where('id_room', $request['id_room'])->first();
            
            $post_data = array(
                'from' => $message->from,
                'to' => ($message->to == Auth::user()->uuid ? $message->from : $message->to),
                'is_send' => Auth::user()->uuid, 
                'message' => htmlspecialchars($request['message']),
                'time' => now()->format('H:i')
            );
            if($request->hasFile('file')) {
                $post_data['type'] = 'file';
                $file = $request->file('file');
                $ext = $file->getClientOriginalExtension();
                $name = now()->format('Y-m-d').'-'.rand(100000000, 999999999).'.'.$ext;
                $file->move(public_path('assets/messages/media/'.$request['id_room']), $name);
                $url_file = asset('assets/messages/media/'.$request['id_room'].'/'.$name);
                $post_data['file'] = $url_file;
            }else {
                $post_data['type'] = 'text';
            }

            $value = json_decode($message->value);
            array_push($value, $post_data);
            $message->value = $value;

            if($message->save()) {

                $notification = array(
                    'uuid' => Str::uuid(32),
                    'to' => ($message->to == Auth::user()->uuid ? $message->from : $message->to),
                    'is_send' => Auth::user()->uuid,
                    'from' => Auth::user()->uuid,
                    'type' => 'pesan',
                    'value' => json_encode($post_data),
                    'status_read' => 0
                );

                Notification::create($notification);

                event(new NotifikasiPesan($notification));
                event(new LiveChat($post_data));

                return response()->json([
                    'status' => true,
                    'error' => false,
                    'detail' => $post_data
                ], 200);
            }
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
    public function store_toko(Request $request) {
        try {

            $message = Message::with('toko')->where('id_room', $request['id_room'])->first();

            $post_data = array(
                'uuid_user' => $message->uuid_user,
                'kode_toko' => $message->kode_toko,
                'is_toko' => true,
                'is_user' => false,
                'message' => htmlspecialchars($request['message']),
                'time' => now()->format('H:i')
            );

            if($request->hasFile('file')) {
                $post_data['type'] = 'file';
                $file = $request->file('file');
                $ext = $file->getClientOriginalExtension();
                $name = now()->format('Y-m-d').'-'.rand(100000000, 999999999).'.'.$ext;
                $file->move(public_path('assets/messages/media/'.$request['id_room']), $name);
                $url_file = asset('assets/messages/media/'.$request['id_room'].'/'.$name);
                $post_data['file'] = $url_file;
            }else {
                $post_data['type'] = 'text';
            }

            $value = json_decode($message->value);
            array_push($value, $post_data);
            $message->value = $value;

            if($message->save()) {

                $notification = array(
                    'uuid' => Str::uuid(32),
                    'to' => $message->user->uuid,
                    'from' => Auth::user()->uuid,
                    'type' => 'pesan',
                    'value' => json_encode($post_data),
                    'status_read' => 0
                );

                Notification::create($notification);

                event(new NotifikasiPesan($notification));
                event(new LiveChat($post_data));

                return response()->json([
                    'status' => true,
                    'error' => false,
                    'detail' => $post_data,
                    'test' => $redis
                ], 200);
            }
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
}
