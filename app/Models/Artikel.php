<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Artikel extends Model
{
    use HasFactory;
    protected $table = 'artikel';
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($artikel) {
            $artikel->created_by = Auth::user()->uuid;
        });
    }

    public function kategori() {
        return $this->belongsTo(KategoriArtikel::class, 'kategori_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'created_by', 'uuid');
    }
}
