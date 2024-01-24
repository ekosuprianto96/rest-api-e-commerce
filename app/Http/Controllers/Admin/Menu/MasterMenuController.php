<?php

namespace App\Http\Controllers\Admin\Menu;

use App\Models\MsMenu;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\API\Handle\ErrorController;
use App\Models\MsMenuRole;
use App\Models\Role;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class MasterMenuController extends Controller
{
    public function index() {
        return view('admin.ms-menu.index');
    }

    public function data_menu(Request $request) {
        try {
            
            $menu = MsMenu::selectRaw('ms_menus.*, menu_parents.nama as nama_parent')
                            ->join('menu_parents', 'ms_menus.id_parent', 'menu_parents.id')
                            ->orderBy('ms_menus.id', 'desc')->get();

            $data = DataTables::of($menu)
                    ->addColumn('id', function($list) {
                        return $list->id;
                    })
                    ->addColumn('nama', function($list) {
                        return $list->nama;
                    })
                    ->addColumn('nama_alias', function($list) {
                        return $list->nama_alias;
                    })
                    ->addColumn('url', function($list) {
                        return $list->url;
                    })
                    ->addColumn('icon', function($list) {
                        return $list->icon;
                    })
                    ->addColumn('nama_parent', function($list) {
                        return $list->nama_parent;
                    })
                    ->addColumn('status', function($list) {
                        if($list->an > 0) {
                            $status = 'Aktif';
                        }else {
                            $status = 'Non Aktif';
                        }

                        return $status;
                    })
                    ->addColumn('tanggal', function($list) {
                        return $list->created_at->format('Y-m-d');
                    })
                    ->addColumn('action', function($list) {
                        return '<div class="d-flex align-items-center justify-content-center" style="gap: 7px;">
                                    <a href="'.route('admin.ms-menu.setting', $list->id).'" class="btn btn-sm btn-warning text-nowrap" style="font-size: 0.8em"><i class="fa fa-cogs"></i> Setting</a>
                                    <a href="javascript:void(0)" onclick="deletedMenu('."'".$list->id."'".')" class="btn btn-sm btn-danger text-nowrap" style="font-size: 0.8em"><i class="fa fa-trash"></i> Hapus</a>
                                </div>';
                    })->rawColumns(['id', 'nama', 'nama_alias', 'url', 'icon', 'nama_parent', 'status', 'tanggal', 'action'])
                    ->make(true);

            return $data;
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function create() {
        return view('admin.ms-menu.create');
    }

    public function store(Request $request) {
        $request->validate([
            'nama_menu' => 'required|string',
            'nama_alias' => 'required|string',
            'url' => 'required|string',
            'icon' => 'required|string',
            'parent' => 'required'
        ]);

        try {
            if(empty($request->parent)) {
                Alert::warning('Maaf', 'Silahkan pilih parent menu a/u Tambah parent menu jika belum tersedia.');
                return redirect()->back();
            }
            
            // $folderName = Str::title($request->nama_menu);
            // $folderName = str_replace(' ', '', $folderName);
            // $path = resource_path('views').'/admin/'.$folderName;
            // $content = "@extends('layouts.main', ['title' => 'Title'])\n\n@section('content')\n\n@endsection";
            // File::makeDirectory($path);
            // File::put($path.'/'.str_replace(' ', '-', $request->nama_menu).'.'.'blade'.'.'.'php', $content);
            $menu = new MsMenu();
            $menu->nama = $request->nama_menu;
            $menu->nama_alias = Str::slug($request->nama_alias);
            $menu->url = $request->url;
            $menu->icon = $request->icon;
            $menu->id_parent = $request->parent;
            if(isset($request->status)) {
                $menu->an = $request->status;
            }else {
                $menu->status = 0;
            }

            if($menu->save()) {
                Alert::success('Sukses', 'Menu berhasil di tambahkan.');
                return redirect()->route('admin.ms-menu.index');
            }

            Alert::error('Gagal', 'Mohon maaf, data gagal disimpan, silahkan coba kembali.');
            return redirect()->route('admin.ms-menu.index');
        }catch(\Exception $err) {
            return ErrorController::getError($err);
        }
    }

    public function setting($id) {
        $menu = MsMenu::find($id);
        $menu_roles = [];
        if(count($menu->role) > 0) {
            foreach($menu->role as $role) {
                $menu_roles[$role->id] = $role;
            }
        }
        $roles = Role::all();
        return view('admin.ms-menu.setting', compact('menu', 'roles', 'menu_roles'));
    }

    public function update(Request $request, $id_menu) {
        try {
            $menu = MsMenu::find($id_menu);
            
            if(empty($menu)) {
                Alert::error('Gagal!', 'Mohon Maaf, Data Gagal Disimpan, Silahkan Coba Kembali.');
                return redirect()->back();
            }

            MsMenuRole::where([
                'ms_menu_id' => $id_menu
            ])->delete();
            foreach($request->roles as $role) {
                if(isset($role)) {
                    MsMenuRole::create([
                        'ms_menu_id' => $id_menu,
                        'role_id' => $role
                    ]);
                }
            }
            $menu->id_parent = $request->parent;
            $menu->save();

            Alert::success('Sukses!', 'Berhasil Setting Menu.');
            return redirect()->back();
        }catch(\Exception $err) {
            return ErrorController::getError($err);
        }
    }

    public function destroy(Request $request) {
        try {
            $msMenu = MsMenu::find($request->id);

            if(empty($msMenu)) {
                return response()->json([
                    'status' => false,
                    'error' => false,
                    'message' => 'Data Not Found.',
                    'detail' => null
                ]);
            }

            $msMenu->delete();

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil menghapus menu.',
                'detail' => 1
            ], 200);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
}
