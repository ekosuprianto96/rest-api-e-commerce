<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuParent extends Model
{
    use HasFactory;
    protected $table = 'menu_parents';
    protected $guarded = ['id'];
}
