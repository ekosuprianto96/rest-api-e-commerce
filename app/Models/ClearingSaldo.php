<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClearingSaldo extends Model
{
    use HasFactory;
    protected $table = 'clearing_saldo';
    protected $guarded = ['id'];
}
