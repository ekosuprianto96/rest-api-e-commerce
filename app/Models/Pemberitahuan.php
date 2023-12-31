<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemberitahuan extends Model
{
    use HasFactory;
    protected $table = 'pemberitahuan';
    protected $guarded = ['id'];

    public function user() {
        return $this->belongsTo(User::class, 'uuid_user', 'uuid');
    }
}
