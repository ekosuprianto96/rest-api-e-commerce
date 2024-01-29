<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsMenu extends Model
{
    use HasFactory;

    public function role() {
        return $this->belongsToMany(Role::class, 'ms_menu_role', 'ms_menu_id', 'role_id', 'id');
    }
}
