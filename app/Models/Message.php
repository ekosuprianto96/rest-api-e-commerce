<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $table = 'message';
    protected $guarded = ['id'];
    protected $primaryKey = 'id_room';
    public $keyType = 'string';
    public $incrementing = false;
    

    
    public function users() {
        return $this->belongsToMany(User::class, 'message_user', 'message_id', 'uuid_user', 'id_room');
    }
    public function toUser() {
        return $this->belongsTo(User::class, 'to', 'uuid');
    }
    public function fromUser() {
        return $this->belongsTo(User::class, 'from', 'uuid');
    }
}
