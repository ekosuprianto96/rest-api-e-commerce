<?php

namespace App\Http\Controllers\Admin\Menu;

use App\Http\Controllers\API\Handle\ErrorController;
use App\Http\Controllers\Controller;
use App\Models\MenuParent;
use Illuminate\Http\Request;

class ParentMenuController extends Controller
{
    public function index() {

    }

    public function store(Request $request) {
        $request->validate([
            'nama' => 'required|string',
            'alias' => 'required|string',
            'order' => 'required|numeric'
        ]);

        try {
            $parent = new MenuParent();
            $parent->nama = $request->nama;
            $parent->nama_alias = $request->alias;
            $parent->order = $request->order;
            $parent->save();

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Prent Menu Berhasil Ditambah.',
                'detail' => 1
            ], 200);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
}
