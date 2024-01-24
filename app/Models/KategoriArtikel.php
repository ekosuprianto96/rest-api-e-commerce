<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriArtikel extends Model
{
    use HasFactory;
    protected $table = 'kategori_artikel';
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($artikel) {
            $artikel->created_by = Auth::user()->uuid;
        });
    }
    
    public function artikel() {
        return $this->hasMany(Artikel::class, 'kategori_id', 'id');
    }
}
