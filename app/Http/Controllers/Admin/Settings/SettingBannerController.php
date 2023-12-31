<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Models\MsBanner;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\API\Handle\ErrorController;

class SettingBannerController extends Controller
{
    public function index() {
        return view('admin.settings.banner.index');
    }

    public function dataBanner(Request $request) {
        try {
            $banner = MsBanner::all();

            $data = DataTables::of($banner)
                    ->addColumn('image', function($list) {
                        return '<img width="100" src="'.$list->image.'" alt="" />';
                    })
                    ->addColumn('title', function($list) {
                        return $list->title;
                    })
                    ->addColumn('uploadBy', function($list) {
                        return $list->userUpload->username;
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
                                    <a href="javascript:void(0)" onclick="editBanner('."'".$list->id."'".')" class="btn btn-sm btn-success text-nowrap" style="font-size: 0.8em"><i class="fa fa-edit"></i> Edit</a>
                                    <a href="javascript:void(0)" onclick="deletedBanner('."'".$list->id."'".')" class="btn btn-sm btn-danger text-nowrap" style="font-size: 0.8em"><i class="fa fa-trash"></i> Hapus</a>
                                </div>';
                    })->rawColumns(['id', 'image', 'title', 'uploadBy', 'tanggal', 'status', 'action'])
                    ->make(true);

            return $data;

        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required',
            'image' => 'required|image|mimes:jpg,png,svg,webp,jpeg'
        ]);
        try {
            $banner = new MsBanner();
            $banner->title = Str::title($request->title);
            $banner->an = $request->status;
            $banner->uuid_user = Auth::user()->uuid;

            if($request->hasFile('image')) {
                $file = $request->file('image');
                $ext = $file->getClientOriginalExtension();
                $newName = date('Ymd').'-'.rand(10000, 99999).'.'.$ext;
                $path = 'assets/admin/banners';
                $file->move(public_path($path), $newName);
                $banner->image = asset($path.'/'.$newName);
            }

            $banner->save();

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil upload Banner.',
                'detail' => 1
            ], 200);

        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function edit(Request $request) {
        try {
            $banner = MsBanner::find($request->id);

            if(empty($banner)) {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Data not found',
                    'detail' => null
                ]);
            }

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil get banner',
                'detail' => $banner
            ], 200);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function update(Request $request) {
        $request->validate([
            'title' => 'required'
        ]);
        try {
            $banner = MsBanner::find($request->id);

            if(empty($banner)) {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Data not found',
                    'detail' => null
                ]);
            }

            $banner->title = Str::title($request->title);
            $banner->an = $request->status;
            $banner->uuid_user = Auth::user()->uuid;

            if($request->hasFile('image')) {
                $path = 'assets/admin/banners';
                File::delete(public_path($path).'/'.$banner->image);
                $file = $request->file('image');
                $ext = $file->getClientOriginalExtension();
                $newName = date('Ymd').'-'.rand(10000, 99999).'.'.$ext;
                $file->move(public_path($path), $newName);
                $banner->image = asset($path.'/'.$newName);
            }

            $banner->save();

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil update Banner.',
                'detail' => 1
            ], 200);

        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function destroy(Request $request) {
        try {
            $banner = MsBanner::find($request->id);

            if(empty($banner)) {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Data not found',
                    'detail' => null
                ]);
            }

            $banner->delete();

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil hapus Banner.',
                'detail' => 1
            ], 200);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
}
