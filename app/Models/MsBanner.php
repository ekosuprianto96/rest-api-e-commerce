<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsBanner extends Model
{
    use HasFactory;

    public function userUpload() {
        return $this->belongsTo(User::class, 'uuid_user', 'uuid');
    }
}
