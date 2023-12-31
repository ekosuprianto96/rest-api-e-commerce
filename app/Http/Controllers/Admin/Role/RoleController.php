<?php

namespace App\Http\Controllers\Admin\Role;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\API\Handle\ErrorController;

class RoleController extends Controller
{
    public function index() {
        return view('admin.role.index');
    }

    public function create() {
        return view('admin.role.create');
    }

    public function data_role(Request $request) {
        try {
            $where = '1=1';

            // if(isset($request->type_pembayaran)) {
            //     $where .= " and type_pembayaran = '".$request->type_pembayaran."'";
            // }
            // if(isset($request->tanggal_mulai)) {
            //     $where .= " and DATE_FORMAT(created_at, '%Y-%m-%d') >= '$request->tanggal_mulai'";
            // }
            // if(isset($request->tanggal_akhir)) {
            //     $where .= " and DATE_FORMAT(created_at, '%Y-%m-%d') <= '$request->tanggal_akhir'";
            // }
            
            // dd($where);
            $data_role = Role::whereRaw($where)->latest()->get();
            // dd($trx_withdraw);
            $data = DataTables::of($data_role)
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
                                    <a data-role="'.$list->id.'" onClick="showFormCreate(event)" href="javascript:void(0)" class="btn btn-create-role btn-sm btn-success text-nowrap" style="font-size: 0.8em"><i data-role="'.$list->id.'" class="fa fa-edit"></i> Edit</a>
                                    <a data-role="'.$list->id.'" onClick="deletedRole(event)" href="javascript:void(0)" class="btn btn-create-role btn-sm btn-danger text-nowrap" style="font-size: 0.8em"><i data-role="'.$list->id.'" class="fa fa-trash"></i> Hapus</a>
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
            $role = Role::with(['permissions'])->where([
                'id' => $request->id_role
            ])->first();

            if(empty($role)) {
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
                'message' => 'berhasil get data role',
                'detail' => $role
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
            $role = Role::find($request->id_role);

            if(empty($role)) {
                return response()->json([
                    'status' => false,
                    'error' => false,
                    'message' => 'Data Note Found.',
                    'detail' => []
                ], 404);
            }

            $role->name = Str::slug($request->nama);
            $role->display_name = $request->nama_alias;
            $role->description = $request->description;
            $role->save();
            
            $role->syncPermissions($request->get('permission') ?? []);

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil Update Role.',
                'detail' => $role
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

            $role = new Role();
            $role->name = Str::slug($request->nama);
            $role->display_name = $request->nama_alias;
            $role->description = $request->description;
            $role->save();

            $role->syncPermissions($request->permission ?? []);

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil Menanmbahkan Role.',
                'detail' => $role
            ], 200);

        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function destroy(Request $request) {
        try {
            $role = Role::find($request->role_id);

            if(empty($role)) {
                return response()->json([
                    'status' => false,
                    'error' => false,
                    'message' => 'Data Note Found.',
                    'detail' => []
                ], 404);
            }

            $role->delete();

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil Menghapus Role.',
                'detail' => 1
            ], 200);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
}
