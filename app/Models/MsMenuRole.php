<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsMenuRole extends Model
{
    use HasFactory;
    protected $table = 'ms_menu_role';
    protected $guarded = ['id'];
}
