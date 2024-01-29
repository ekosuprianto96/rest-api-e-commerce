<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaksiAccount extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function user() {
        return $this->belongsTo(User::class, 'uuid_user', 'uuid');
    }
    public function bank() {
        return $this->belongsTo(PaymentMethod::class, 'method', 'kode_payment');
    }
    public function get_saldo($type = 'gateway', $method = ['bank_transfer']) {
        $total_debit = $this->where([
                            'type_payment' => $type,
                            'method' => $method,
                            'jns_payment' => 'DEBIT'
                        ])->sum('total');
        $total_credit = $this->where([
                            'type_payment' => $type,
                            'method' => $method,
                            'jns_payment' => 'CREDIT'
                        ])->sum('total');
        return (float) $total_debit - $total_credit;
    }
}
