<?php

namespace App\Http\Controllers\Admin\Permission;

use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\API\Handle\ErrorController;

class PermissionController extends Controller
{
    public function index() {
        return view('admin.permission.index');
    }

    public function data_permission(Request $request) {
        try {
            $where = '1=1';

            $permissions = Permission::latest()->get();

            $data = DataTables::of($permissions)
                    ->addColumn('nama', function($list) {
                        return $list->name;
                    })
                    ->addColumn('nama_alias', function($list) {
                        return $list->display_name;
                    })
                    ->addColumn('description', function($list) {
                        return $list->description;
                    })
                    ->addColumn('tanggal', function($list) {
                        return $list->created_at->format('Y-m-d');
                    })
                    ->addColumn('action', function($list) {
                        return '<div class="d-flex align-items-center justify-content-center" style="gap: 7px;">
                                    <a data-permission="'.$list->id.'" onClick="showFormEdit(event)" href="javascript:void(0)" class="btn btn-create-role btn-sm btn-success text-nowrap" style="font-size: 0.8em"><i data-permission="'.$list->id.'" class="fa fa-edit"></i> Edit</a>
                                    <a data-permission="'.$list->id.'" onClick="deletedPermission(event)" href="javascript:void(0)" class="btn btn-create-role btn-sm btn-danger text-nowrap" style="font-size: 0.8em"><i data-permission="'.$list->id.'" class="fa fa-trash"></i> Hapus</a>
                                </div>';
                    })->rawColumns(['nama', 'nama_alias', 'description', 'tanggal', 'action'])
                    ->make(true);

            return $data;
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function edit(Request $request) {
        try {
            $permission = Permission::where('id', $request->id_permission)->first();

            if(empty($permission)) {
                return response()->json([
                    'status' => false,
                    'error' => false,
                    'message' => 'Data Note Found.',
                    'detail' => []
                ], 404);
            }

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'berhasil get data permission',
                'detail' => $permission
            ], 200);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function store(Request $request) {
        $request->validate([
            'nama' => 'required',
            'nama_alias' => 'required',
            'description' => 'required',
        ]);
        try {
            $permission = new Permission();
            $permission->name = $request->nama;
            $permission->display_name = $request->nama_alias;
            $permission->description = $request->description;
            $permission->save();

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil create permission.',
                'detail' => $permission
            ], 200);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function update(Request $request) {
        $request->validate([
            'nama' => 'required',
            'nama_alias' => 'required',
            'description' => 'required',
        ]);
        try {
            $permission = Permission::find($request->permission_id);

            if(empty($permission)) {
                return response()->json([
                    'status' => false,
                    'error' => false,
                    'message' => 'Data Note Found.',
                    'detail' => []
                ], 404);
            }

            $permission->name = Str::slug($request->nama);
            $permission->display_name = $request->nama_alias;
            $permission->description = $request->description;
            $permission->save();

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil Update Permission.',
                'detail' => $permission
            ], 200);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function destroy(Request $request) {
        try {
            $permission = Permission::find($request->permission_id);

            if(empty($permission)) {
                return response()->json([
                    'status' => false,
                    'error' => false,
                    'message' => 'Data Note Found.',
                    'detail' => []
                ], 404);
            }

            $permission->delete();

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil Menghapus Permission.',
                'detail' => 1
            ], 200);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
}
