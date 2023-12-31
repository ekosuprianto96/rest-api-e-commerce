<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifikasiPengguna extends Model
{
    use HasFactory;
    protected $table = 'notifikasi_pengguna';
    protected $guarded = ['id'];


    public function updateBy() {
        return $this->belongsTo(User::class, 'updateBy', 'uuid');
    }
}
