<?php

namespace App\View\Components;

use App\Models\MenuParent;
use App\Models\NotifikasiAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class MenuSidebar extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $title;
    public function __construct($title)
    {
        $this->title = $title;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $roles = Auth::user()->roles ?? null;
        
        $array_menu = array();
        foreach($roles as $role) {
            $menus = $role->menus;
            if(isset($menus)) {
                foreach($menus as $menu) {
                    $parent = MenuParent::where([
                        'id' => $menu->id_parent
                    ])->first();
                    
                    $menu['nama_parent'] = $parent->nama;
                    $menu['order'] = $parent->order;
                    $menu['notif'] = NotifikasiAdmin::where([
                        'type' => $menu->nama_alias,
                        'status_read' => 0
                    ])->count();
                    $array_menu[] = $menu;
                }
            }
        }
        
        $array_menu = collect($array_menu)->where('an', 1)->sortBy('order')->groupBy('nama_parent');
    
        return view('components.admin.menu-sidebar', compact('array_menu'));
    }
}
