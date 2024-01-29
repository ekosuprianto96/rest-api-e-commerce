<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaktuProsesOrder extends Model
{
    use HasFactory;
    protected  $guarded = ['id'];

    public function order() {
        return $this->belongsTo(DetailOrder::class, 'order_id', 'id');
    }
}
