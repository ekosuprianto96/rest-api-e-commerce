<?php

namespace App\Http\Controllers\Admin\Notifikasi;

use App\Http\Controllers\API\Handle\ErrorController;
use Illuminate\Http\Request;
use App\Models\NotifikasiPengguna;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class NotifikasiController extends Controller
{
    public function index() {
        return view('admin.settings.notifikasi.index');
    }

    public function update(Request $request) {
        try {
            $notifikasi = NotifikasiPengguna::where('type', $request->type)->first();

            if(empty($notifikasi)) {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Dat Not Found.',
                    'detail' => null
                ]);
            }

            $notifikasi->title = $request->title;
            $notifikasi->content = nl2br($request->content);
            $notifikasi->an = $request->status;

            if($request->hasFile('image')) {
                $path = 'assets/admin/notifikasi/image';
                File::delete(public_path($path).'/'.$notifikasi->image);
                $file = $request->file('image');
                $ext = $file->getClientOriginalExtension();
                $newName = date('Ymd').'-'.rand(10000, 99999).'.'.$ext;
                $file->move(public_path($path), $newName);
                $notifikasi->image = asset($path.'/'.$newName);
            }

            $notifikasi->save();

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil update notifikasi.',
                'detail' => 1
            ]);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function renderViewPopup() {
        try {
            $popup = NotifikasiPengguna::where('type', 1)->first();

            return view('admin.render.popup', compact('popup'));
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function renderViewBartop() {
        
        $bartop = NotifikasiPengguna::where('type', 2)->first();
        return view('admin.render.bartop', compact('bartop'));
    }
}
