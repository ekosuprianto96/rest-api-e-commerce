<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormProduk extends Model
{
    use HasFactory;
    protected $table = 'form_produk';
    protected $guarded = ['id'];
}
