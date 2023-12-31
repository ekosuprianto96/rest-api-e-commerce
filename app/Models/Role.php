<?php

namespace App\Models;

use Laratrust\Models\LaratrustRole;

class Role extends LaratrustRole
{
    public $guarded = ['id'];

    public function user() {
        return $this->belongsToMany(User::class, 'role_user', 'user_id', 'role_id', 'id');
    }
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id', 'id');
    }

    public function menus() {
        return $this->belongsToMany(MsMenu::class, 'ms_menu_role', 'role_id', 'ms_menu_id', 'id');
    }
}
