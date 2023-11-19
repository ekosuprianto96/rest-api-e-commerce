<?php

namespace App\Events;

use App\Models\User;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class LiveChat implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    // use InteractsWithBroadcasting, Dispatchable;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $message;
    // public $user;
    // public $kode_toko;
    public function __construct($message)
    {
        // $this->broadcastVia('pusher');
        $this->message = $message;
        // $this->kode_toko = $kode_toko;
        // $this->user = $user;
        // $this->dontBroadCastToCurrentUser();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['message'];
    }
    public function broadcastAs()
    {
        if($this->message['is_user']) {
            return 'live-chat-'.$this->message['kode_toko'];
        }else {
            return 'live-chat-'.$this->message['uuid_user'];
        };
    }
}
