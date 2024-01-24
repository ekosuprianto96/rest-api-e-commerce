<?php

namespace App\Http\Controllers\Admin\Artikel;

use App\Models\Artikel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\KategoriArtikel;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\API\Handle\ErrorController;

class ArtikelController extends Controller
{
    public function index() {
        return view('admin.artikel.index');
    }

    public function create() {
        $kategori = KategoriArtikel::all();
        return view('admin.artikel.create', compact('kategori'));
    }

    public function edit($slug) {
        $kategori = KategoriArtikel::all();
        $artikel = Artikel::where('slug', $slug)->first();
        return view('admin.artikel.edit', compact('kategori', 'artikel'));
    }

    public function viewKategori() {
        $kategori = KategoriArtikel::all();
        return view('admin.artikel.kategori', compact('kategori'));
    }

    public function update(Request $request, $slug) {
        $request->validate([
            'nama_display' => 'required|string|max:20',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'kategori' => 'required',
            'body' => 'required'
        ]);

        try {
            DB::beginTransaction();
            $artikel = Artikel::where('slug', $slug)->first();
            
            if(empty($artikel)) {
                Alert::warning('Mohon Maaf!', 'Data tidak ditemukan.');
                return redirect()->back();
            }

            $artikel->display_name = Str::title($request->nama_display);
            $artikel->title = Str::title($request->title);
            $artikel->kategori_id = $request->kategori;
            $artikel->slug = $request->slug;
            $artikel->uuid = Str::uuid(32);
            $artikel->body = nl2br($request->body);
            $artikel->save();

            Alert::success('Sukses!', 'Artikel berhasil diupdate.');
            DB::commit();
            return redirect()->route('admin.artikel.index');
        }catch(\Exception $err) {
            DB::rollBack();
            return ErrorController::getError($err);
        }
    }

    public function dataArtikel(Request $request) {
        try {
            $where = '1=1';
            $menu = Artikel::with(['kategori'])->whereRaw($where)->get();

            $data = DataTables::of($menu)
                    ->addColumn('nama', function($list) {
                        return $list->display_name;
                    })
                    ->addColumn('title', function($list) {
                        return $list->title;
                    })
                    ->addColumn('kategori', function($list) {
                        return $list->kategori->nama;
                    })
                    ->addColumn('created_by', function($list) {
                        return $list->user->full_name;
                    })
                    ->addColumn('tanggal', function($list) {
                        return $list->created_at->format('Y-m-d');
                    })
                    ->addColumn('action', function($list) {
                        return '<div class="d-flex align-items-center justify-content-center" style="gap: 7px;">
                                    <a href="'.url('admin/artikel/edit', $list->slug).'" class="btn btn-sm btn-success text-nowrap" style="font-size: 0.8em"><i class="fa fa-edit"></i> Edit</a>
                                    <a href="javascript:void(0)" onclick="deletedArtikel('."'".$list->slug."'".')" class="btn btn-sm btn-danger text-nowrap" style="font-size: 0.8em"><i class="fa fa-trash"></i> Hapus</a>
                                </div>';
                    })->rawColumns(['nama', 'title', 'kategori', 'created_by', 'tanggal', 'action'])
                    ->make(true);

            return $data;
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function store(Request $request) {
        $request->validate([
            'nama_display' => 'required|string|max:20',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:artikel',
            'kategori' => 'required',
            'body' => 'required'
        ]);

        try {
            DB::beginTransaction();
            $artikel = new Artikel();
            $artikel->display_name = Str::title($request->nama_display);
            $artikel->title = Str::title($request->title);
            $artikel->kategori_id = $request->kategori;
            $artikel->slug = $request->slug;
            $artikel->uuid = Str::uuid(32);
            $artikel->body = nl2br($request->body);
            $artikel->save();

            Alert::success('Sukses!', 'Artikel berhasil dibuat.');
            DB::commit();
            return redirect()->route('admin.artikel.index');
        }catch(\Exception $err) {
            DB::rollBack();
            return ErrorController::getError($err);
        }
    }
    public function storeKategori(Request $request) {
        $request->validate([
            'nama' => 'required|string|max:20',
            'alias' => 'required|string|max:255',
            'order' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            $artikel = new KategoriArtikel();
            $artikel->nama = Str::title($request->nama);
            $artikel->nama_alias = Str::title($request->alias);
            $artikel->order = $request->order;
            $artikel->save();

            DB::commit();
            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil Tambah Kategori.',
                'detail' => 1
            ], 200);
        }catch(\Exception $err) {
            DB::rollBack();
            return ErrorController::getResponseError($err);
        }
    }

    public function destroy(Request $request) {
        try {
            $artikel = Artikel::where('slug', $request->slug)->first();

            if(empty($artikel)) {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Data artikel tidak ditemukan',
                    'detail' => null
                ]);
            }

            $artikel->delete();

            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Artikel berhasil dihapus.',
                'detail' => null
            ]);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
}
