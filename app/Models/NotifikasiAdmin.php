<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifikasiAdmin extends Model
{
    use HasFactory;
    protected $table = 'notifikasi_admin';
    protected $guarded = ['id'];
}
