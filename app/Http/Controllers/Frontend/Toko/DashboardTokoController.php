<?php

namespace App\Http\Controllers\Frontend\Toko;

use App\Models\Produk;
use App\Models\DetailToko;
use App\Models\FormProduk;
use App\Models\DetailOrder;
use App\Models\ImageProduk;
use Illuminate\Support\Str;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Charts\Toko\OrderToko;
use App\Models\ListFormProduk;
use App\Models\SettingWebsite;
use App\Models\NotifikasiAdmin;
use App\Events\NotificationAdmin;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\DetailTokoController;
use App\Http\Controllers\API\Handle\ErrorController;

class DashboardTokoController extends Controller
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function index(OrderToko $chart) {

        $chart = $chart->build();
        
        return view('frontend.toko.index', compact('chart'));
    }

    public function editProduk($kodeproduk) {

        $produk = Produk::where('kode_produk', $kodeproduk)->first();

        if(empty($produk)) {

            Alert::warning('Maaf', 'Produk tidak ditemukan atau sudah dihapus.');

            return redirect()->back();
        }
        

        if(!session()->has('produkTempEdit')) {
            $paramSession = [
                'step' => 1
            ];
    
            session()->put('produkTempEdit', $paramSession);
        }

        return view('frontend.toko.edit-produk', compact('produk'));
    }

    public function destroyProduk(Request $request) {
        try {

            $produk = Produk::where('kode_produk', $request->kodeproduk)->first();

            if(empty($produk)) {
                Alert::warning('Gagal!', 'Mohon maaf, data produk tidak ditemukan atau sudah dihapus.');

                return redirect()->back();
            }

            $produk->an = 0;
            $produk->save();

            Alert::success('Sukses!', 'Produk '.$produk->nm_produk.' berhasil dihapus dari daftar.');
            return redirect()->back();
        }catch(\Exception $err) {
            return ErrorController::getError($err);
        }
    }

    public function dataOrder() {
        try {

            $detail_orders = DetailOrder::with(['order', 'produk'])->where([
                'kode_toko' => self::getToko('kode_toko')
            ])->latest()->get();

            $data = DataTables::of($detail_orders)
                ->addColumn('no_order', function ($list) {
                    return $list->no_order;
                })
                ->addColumn('nama_produk', function ($list) {
                    return $list->produk->nm_produk;
                })
                ->addColumn('total_biaya', function ($list) {
                    return $list->total_biaya;
                })
                ->addColumn('nama_pembeli', function ($list) {
                    return $list->user->full_name;
                })
                ->addColumn('tanggal', function ($list) {
                    return $list->created_at->format('Y-m-d');
                })
                ->addColumn('action', function ($list) {
                    return '<div class="d-flex align-items-center justify-content-center" style="gap: 7px;">
                                    <a href="' . route('admin.produk.view-produk', $list->produk->kode_produk) . '" class="btn btn-sm btn-primary text-nowrap" style="font-size: 0.8em"><i class="fa fa-eye"></i> Detail</a>
                                </div>';
                })->rawColumns(['no_order', 'nama_produk', 'total_biaya', 'nama_pembeli', 'kategori', 'tanggal', 'action'])
                ->make(true);

            return $data;

        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public static function getToko($key = null) {
        
        if(empty($key)) return Auth::user()->toko ?? null;

        return Auth::user()->toko->{$key};
    } 

    public function daftarOrder() {

        $detail_orders = DetailOrder::with(['order', 'produk'])->where([
            'kode_toko' => self::getToko('kode_toko')
        ])->latest()->get();

        $array_response = [];
        foreach ($detail_orders as $order) {
            $produk = Produk::where('kode_produk', $order->kode_produk)->first();

            $notif_order = Notification::where([
                'type' => 'order_toko',
                'to' => $produk->toko->user->uuid
            ])->first();

            if (isset($notif_order)) {
                $notif_order->delete();
            }

            $harga_produk = $produk->getHargaDiskon($produk);
            if ($order->created_at->format('Y-m-d') == now()->format('Y-m-d')) {
                $order->status_new_order = 1;
            } else {
                $order->status_new_order = 0;
            }

            $orderData = [
                'id' => $order->id,
                'no_order' => $order->no_order,
                'biaya_platform' => number_format($order->potongan_platform, 0),
                'total_biaya' => number_format($order->total_biaya, 0),
                'total_diskon' => number_format($order->potongan_diskon, 0),
                'potongan_referal' => number_format($order->potongan_referal, 0),
                'tanggal' => $order->created_at->format('Y-m-d'),
                'status_order' => $order->status_order,
                'status_pembayaran' => $order->order->status_order,
                'status_new_order' => $order->status_new_order,
            ];
            $produkData = [
                'image' => $order->produk->images[0]->url,
                'nama_produk' => $order->produk->nm_produk,
                'kode_produk' => $order->produk->kode_produk,
                'harga' => $harga_produk,
                'type_produk' => $order->produk->type_produk,
                'kategori' => $order->produk->kategori->nama_kategori
            ];
            $pemebeli = [
                'nama_pembeli' => $order->user->full_name,
                'uuid_user' => $order->user->uuid
            ];

            $orderList = [
                'order' => $orderData,
                'produk' => $produkData,
                'pembeli' => $pemebeli
            ];

            $array_response[] = $orderList;
        }

        $total_penjualan = $detail_orders->sum('total_biaya');
        $total_terjual = $detail_orders->count();
        $penghasilan = [
            'total_penjualan' => number_format($total_penjualan, 0, 0, '.'),
            'total_terjual' => $total_terjual,
            'dataOrder' => $array_response
        ];

        return view('frontend.toko.daftar-order', compact('penghasilan'));
    }

    public function daftarProduk() {

        $produk = Produk::with(['kategori', 'form', 'images'])->where([
            'kode_toko' => self::getToko('kode_toko'),
            'an' => 1
        ])->latest()->get();

        foreach ($produk as $prd) {
            $prd->total_produk_toko = $prd->toko->produk->count();
            $prd->total_terjual = $prd->order->count();
            $prd->total_terjual_toko = $prd->toko->order->count();
            $prd->harga = $prd->getHargaDiskon($prd);
        }
        
        return view('frontend.toko.daftar-produk', compact('produk'));
    }

    public function uploadProduk() {

        if(!session()->has('produkTemp')) {
            $paramSession = [
                'step' => 1,
                'nama_produk' => null,
                'kategori' => null,
                'diskon' => null,
                'status_diskon' => 0,
                'potongan_persen' => 0,
                'potongan_harga' => 0,
                'harga' => 0,
                'type_produk' => 1,
                'images' => []
            ];
    
            session()->put('produkTemp', $paramSession);
        }
        
        return view('frontend.toko.upload-produk');
    }

    public function updateProduk(Request $request, $kodeproduk) {

        $session = session()->get('produkTempEdit');

        if(isset($session)) {
            if($session['step'] == 1) {
                $request->validate([
                    'nama_produk' => 'required',
                    'type_produk' => 'required',
                    'kategori' => 'required',
                    'potongan_persen' => $this->createValidateRequestWithCondition([
                        'status_diskon' => 'on', 
                        'type_potongan' => 1
                    ], 'required|min:1|max:100'),
                    'potongan_harga' => $this->createValidateRequestWithCondition([
                        'status_diskon' => 'on', 
                        'type_potongan' => 2
                    ], 'required|min:1000'),
                    'harga' => 'required|min:5000|numeric'
                ]);
            }elseif($session['step'] == 2) {
                $request->validate([
                    'komisi_affiliasi' => $this->createValidateRequestWithCondition(['status_affiliasi' => 'on'], 'required'),
                    'garansi' => $this->createValidateRequestWithCondition(['status_garansi' => 'on'], 'required'),
                    'waktu_proses' => $this->createValidateRequestWithCondition(['type_produk' => '2'], 'required'),
                    'file_produk' => $this->createValidateRequestWithCondition(['type_produk' => '2','file_produk' => true], 'required|mimes:zip,txt,pdf,rar,exe'),
                    'deskripsi' => 'required'
                ]);
            }
        }

        try {
            DB::beginTransaction();

            $diskon = 0;

            if($request->status_diskon == 'on') {
                if($request->potongan_persen > 0) {
                    $diskon = $request->potongan_persen;
                }else {
                    $diskon = $request->potongan_harga;
                }
            }

            if($session['step'] == 1) {
                $paramSession = [
                    'step' => 2,
                    'nama_produk' => $request->nama_produk,
                    'kategori' => $request->kategori,
                    'diskon' => $diskon,
                    'status_diskon' => $request->status_diskon,
                    'type_potongan' => $request->type_potongan,
                    'harga' => $request->harga,
                    'type_produk' => $request->type_produk,
                    'images' => $request->images
                ];
    
                session()->put('produkTempEdit', $paramSession);
               
                return redirect()->back();
            }

            $dataProduk = session()->get('produkTempEdit');
            
            $produk = Produk::where('kode_produk', $kodeproduk)->first();
            $produk->nm_produk = Str::title($dataProduk['nama_produk']);
            $produk->slug = Str::slug($produk->nm_produk) . '-' . Str::random(10);
            $produk->kode_toko = self::getToko('kode_toko');
            $produk->kode_kategori = $dataProduk['kategori'];
            $produk->deskripsi = $request['deskripsi'];
            $produk->harga = $dataProduk['harga'];
            $produk->potongan_harga = ($dataProduk['type_potongan'] == 2 ? $dataProduk['diskon'] : 0);
            $produk->potongan_persen = ($dataProduk['type_potongan'] == 1 ? $dataProduk['diskon'] : 0);
            $produk->image = 'no-image.jpg';
            $produk->link_referal = 'https://iorsale.com';
            $produk->waktu_proses = $request->waktu_proses;

            if ($request['garansi'] > 0) {
                $produk->garansi = $request['garansi'];
            }

            if (isset($request['status_affiliasi'])) {
                $produk->status_referal = 1;
                $produk->komisi_referal = intval($request['komisi_affiliasi']);
            }

            if ($request->hasFile('file_produk')) {
                $file = $request->file('file_produk');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path(path_file_produk().'/'.self::getToko('kode_toko')), $fileName);
                $produk->type_produk = 'AUTO';
                $produk->file_name = $fileName;
            } else {
                $produk->type_produk = 'MANUAL';
            }

            if ($produk->save()) {
                $produk->save();

                if(@count($dataProduk['images'])) {
                    foreach($dataProduk['images'] as $image) {
                        $image = ImageProduk::where('uuid', $image)->first();
                        $image->kode_produk = $produk->kode_produk;
                        $image->save();
                    }
                }

                if ($request->input('list_form')) {
                    $form_list = json_decode($request->input('list_form'));
                    foreach ($form_list as $form) {
                        $list_form = new ListFormProduk();
                        $list_form->kode_produk = $produk->kode_produk;
                        $list_form->label = $form->name;
                        $list_form->type = $form->type;
                        $list_form->save();
                    }
                }

                $notification_admin = array(
                    'uuid' => Str::uuid(32),
                    'type' => 'konfirmasi-produk',
                    'target' => 'konfirmasi-produk',
                    'value' => $produk,
                    'status_read' => 0
                );

                NotifikasiAdmin::create($notification_admin);

                event(new NotificationAdmin($notification_admin));

                session()->forget('produkTempEdit');
                DB::commit();
                return redirect()->route('toko.daftar-produk');
            } 

            DB::rollback();
            Alert::error('Gagal Simpan!', 'Data produk tidak berhasil disimpan, silahkan coba lagi.');
            return redirect()->back();

        }catch(\Exception $err) {
            return ErrorController::getError($err);
        }
    }

    public function storeProduk(Request $request) {

        $session = session()->get('produkTemp');

        if(isset($session)) {
            if($session['step'] == 1) {
                $request->validate([
                    'nama_produk' => 'required',
                    'type_produk' => 'required',
                    'kategori' => 'required',
                    'potongan_persen' => $this->createValidateRequestWithCondition([
                        'status_diskon' => 'on', 
                        'type_potongan' => 1
                    ], 'required|min:1|max:100'),
                    'potongan_harga' => $this->createValidateRequestWithCondition([
                        'status_diskon' => 'on', 
                        'type_potongan' => 2
                    ], 'required|min:1000'),
                    'harga' => 'required|min:5000|numeric'
                ]);
            }elseif($session['step'] == 2) {
                $request->validate([
                    'komisi_affiliasi' => $this->createValidateRequestWithCondition(['status_affiliasi' => 'on'], 'required'),
                    'garansi' => $this->createValidateRequestWithCondition(['status_garansi' => 'on'], 'required'),
                    'waktu_proses' => $this->createValidateRequestWithCondition(['type_produk' => '2'], 'required'),
                    'file_produk' => $this->createValidateRequestWithCondition(['type_produk' => '2'], 'required|mimes:zip,txt,pdf,rar,exe'),
                    'deskripsi' => 'required'
                ]);
            }
        }

        try {
            DB::beginTransaction();

            $diskon = 0;

            if($request->status_diskon == 'on') {
                if($request->potongan_persen > 0) {
                    $diskon = $request->potongan_persen;
                }else {
                    $diskon = $request->potongan_harga;
                }
            }

            if($session['step'] == 1) {
                $paramSession = [
                    'step' => 2,
                    'nama_produk' => $request->nama_produk,
                    'kategori' => $request->kategori,
                    'diskon' => $diskon,
                    'status_diskon' => $request->status_diskon,
                    'type_potongan' => $request->type_potongan,
                    'harga' => $request->harga,
                    'type_produk' => $request->type_produk,
                    'images' => $request->images
                ];
    
                session()->put('produkTemp', $paramSession);
               
                return redirect()->back();
            }

            $dataProduk = session()->get('produkTemp');
            
            $produk = new Produk();
            $produk->nm_produk = Str::title($dataProduk['nama_produk']);
            $produk->slug = Str::slug($produk->nm_produk) . '-' . Str::random(10);
            $produk->kode_toko = self::getToko('kode_toko');
            $produk->kode_kategori = $dataProduk['kategori'];
            $produk->deskripsi = $request['deskripsi'];
            $produk->harga = $dataProduk['harga'];
            $produk->potongan_harga = ($dataProduk['type_potongan'] == 2 ? $dataProduk['diskon'] : 0);
            $produk->potongan_persen = ($dataProduk['type_potongan'] == 1 ? $dataProduk['diskon'] : 0);
            $produk->image = 'no-image.jpg';
            $produk->link_referal = 'https://iorsale.com';

            if ($request['garansi'] > 0) {
                $produk->garansi = $request['garansi'];
            }

            if (isset($request['status_affiliasi'])) {
                $produk->status_referal = 1;
                $produk->komisi_referal = intval($request['komisi_affiliasi']);
            }

            if ($request->hasFile('file_produk')) {
                $file = $request->file('file_produk');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path(path_file_produk().'/'.self::getToko('kode_toko')), $fileName);
                $produk->type_produk = 'AUTO';
                $produk->file_name = $fileName;
            } else {
                $produk->type_produk = 'MANUAL';
            }

            if ($produk->save()) {
                $produk->save();

                if(@count($dataProduk['images'])) {
                    foreach($dataProduk['images'] as $image) {
                        $image = ImageProduk::where('uuid', $image)->first();
                        $image->kode_produk = $produk->kode_produk;
                        $image->save();
                    }
                }

                if ($request->input('list_form')) {
                    $form_list = json_decode($request->input('list_form'));
                    foreach ($form_list as $form) {
                        $list_form = new ListFormProduk();
                        $list_form->kode_produk = $produk->kode_produk;
                        $list_form->label = $form->name;
                        $list_form->type = $form->type;
                        $list_form->save();
                    }
                }

                $notification_admin = array(
                    'uuid' => Str::uuid(32),
                    'type' => 'konfirmasi-produk',
                    'target' => 'konfirmasi-produk',
                    'value' => $produk,
                    'status_read' => 0
                );

                NotifikasiAdmin::create($notification_admin);

                event(new NotificationAdmin($notification_admin));

                DB::commit();
                return redirect()->route('toko.daftar-produk');
            } 

            DB::rollback();
            Alert::error('Gagal Simpan!', 'Data produk tidak berhasil disimpan, silahkan coba lagi.');
            return redirect()->back();

        }catch(\Exception $err) {
            return ErrorController::getError($err);
        }
    }

    public function storeImage(Request $request) {
        try {

            $session = null;

            if($request->hasFile('image')) {
                $image = new ImageProduk();
                $file = $request->file('image');
                $ext = $file->getClientOriginalExtension();
                $newName = rand(10000, 99999) . '.' . $ext;
                $path = 'produk/image/' . Auth::user()->toko->kode_toko;
                $file->move(public_path($path), $newName);

                $image->url = asset($path . '/' . $newName);
                $image->uuid = Str::uuid();
                $image->kode_produk = $request->kode_produk;
                $image->an = 1;
                $image->save();

                return response()->json([
                    'status' => true,
                    'error' => false,
                    'message' => 'Sukses',
                    'detail' => $image->uuid
                ]);
            }


        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function createValidateRequestWithCondition($condition = [], $validate = '') {
        $groupValidate = [];
    
        if(count($condition) > 0) {
            foreach($condition as $key => $value) {
                if($this->request->{$key} != $value) {
                    continue;
                } else {
                    array_push($groupValidate, 1);
                }
            }
        }
    
        if(count($groupValidate) === count($condition)) {
            return $validate;
        }
    
        return '';
    }

    public function detailOrder(Request $request) {
        $order_detail = DetailOrder::where([
            'no_order' => $request->no_order,
            'kode_toko' => self::getToko('kode_toko'),
            'id' => $request->vx
        ])->first();

        if(empty($order_detail)) {
            return abort(404);
        }

        $get_produk = Produk::where('kode_produk', $order_detail->kode_produk)->first();
        $harga_produk = $get_produk->getHargaDiskon($get_produk);

        $list_form = [];
        foreach ($order_detail->produk->form as $form) {
            $data_form = FormProduk::where([
                'id_form' => $form->id,
                'kode_produk' => $form->kode_produk,
                'uuid_user' => Auth::user()->uuid
            ])->first();

            $form->value_form = (isset($data_form) ? $data_form->value : '');
            array_push($list_form, $form);
        }
        
        $produk = array(
            'nama_produk' => $order_detail->produk->nm_produk,
            'kategori' => $order_detail->produk->kategori->nama_kategori,
            'diskon' => ($get_produk->potongan_persen > 0 ? $get_produk->potongan_persen : ($get_produk->potongan_harga > 0 ? $get_produk->potongan_harga : 0)),
            'harga_diskon' => $harga_produk['harga_diskon'],
            'harga_real' => $harga_produk['harga_real'],
            'harga_fixed' => $harga_produk['harga_fixed'],
            'komisi_referal' => $order_detail->status_referal ? number_format($order_detail->produk->komisi_referal, 0) : 0,
            'image' => $order_detail->produk->images[0]->url,
            'type_produk' => $order_detail->produk->type_produk,
            'status_referal' => $order_detail->produk->status_referal,
            'form' => $list_form
        );

        $items['produk'] = $produk;

        $biaya_platform = getSettings('biaya_platform');
        $biaya_platform = (float) $biaya_platform / 100;
        $biaya_platform = (float) ($get_produk->getHargaFixed() * $biaya_platform);
        $total_pendapatan = (float) $get_produk->getHargaFixed() - $biaya_platform;
        $status_new_order = 0;

        if ($order_detail->created_at->format('Y-m-d') == now()->format('Y-m-d')) {
            $status_new_order = 1;
        }
        $order = array(
            'vx' => $order_detail->id, 
            'no_order' => $order_detail->no_order,
            'nama_pembeli' => $order_detail->user->full_name,
            'biaya_platform' => getSettings('biaya_platform'),
            'total_pendapatan' => number_format($total_pendapatan, 0, 0, '.'),
            'status_pembayaran' => $order_detail->order->status_order,
            'status_order' => $order_detail->status_order,
            'type_pembayaran' => $order_detail->order->type_payment,
            'waktu_proses' => $get_produk->waktuProses(),
            'status_new_order' => $status_new_order,
            'tanggal' => $order_detail->created_at->format('d M Y'),
            'total_biaya' => number_format($order_detail->total_biaya, 0, 0, '.'),
            'payment' => $order_detail->getPayment()
        );
        $items['order'] = $order;
        
        return view('frontend.toko.detail-order', compact('items'));
    }

    public function updateProsesOrder(Request $request, $noorder) {
        try {

            DB::beginTransaction();
            $order_detail = DetailOrder::where([
                'kode_toko' => self::getToko('kode_toko'),
                'no_order' => $noorder,
                'id' => $request->vx
            ])->first();

            if(empty($order_detail)) {
                Alert::warning('Maaf!', 'Data order ini tidak tersedia a/u sudah dihapus karena melanggar kebijakan kami.');

                return redirect()->back();
            }

            if($request->status_order != '4') {
                if($order_detail->order->status_order === '0' || $order_detail->order->status_order === 'PENDING') {
                    Alert::warning('Maaf!', 'Order tidak bisa di proses karena pembayaran belum dikonfirmasi, silahkan tunggu sampai pembayaran dikonfirmasi oleh Admin!');
    
                    return redirect()->back();
                }
            }

            if($order_detail->status_order === 'CANCEL') {
                Alert::warning('Maaf!', 'Order sudah di cancel dan tidak bisa di proses kembali.');

                return redirect()->back();
            }

            if(!in_array($request->status_order, ['1', '2', '3', '4'])) {
                Alert::warning('Maaf!', 'Pilih status order dengan benar.');

                return redirect()->back();
            }

            // Kondisi jika order mau di update ke sukses
            if($request->status_order == '3') {
                if($request->type_data_order === 'file') {
                    if(empty($request->file_order)) {
                        Alert::warning('Maaf!', 'Data file untuk customer belum di upload');

                        return redirect()->back();
                    }
                }else {
                    if(empty($request->text_order) || $request->text_order == '') {
                        Alert::warning('Maaf!', 'Data text untuk customer belum di kirim');

                        return redirect()->back();
                    }
                }
            }

            $updateStatus = $order_detail->updateStatusOrder($request);
            if(!$updateStatus['status']) {
                Alert::warning('Maaf!', $updateStatus['message']);

                return redirect()->back();
            }

            if(array_key_exists('is_cancel', $updateStatus)) {
                Alert::success('Sukses!', 'Order berhasil dicancel.');
            }else {
                Alert::success('Sukses!', 'Order berhasil di update dengan status '.$order_detail->status_order);
            }

            DB::commit();
            
            return redirect()->back();
        }catch(\Exception $err) {
            return ErrorController::getError($err);
        }
    }
    public function settingsToko() {
        $toko = DetailToko::with('saldo')->where('uuid_user', Auth::user()->uuid)->first();

        $toko->saldo->total_saldo = number_format($toko->saldo->total_saldo, 2, '.');
        $toko->saldo_refaund->total_refaund = number_format($toko->saldo_refaund->total_refaund, 2, '.');

        return view('frontend.toko.profile-toko', compact('toko'));
    }

    public function uploadImageProfileToko(Request $request) {
        $request->validate([
            'image' => 'image|mimes:jpg,png,svg,jpeg,webp|max:30000'
        ]);

        try {

            $toko = DetailToko::where('kode_toko', self::getToko('kode_toko'))->first();

            if ($request->hasFile('image')) {
                if ($toko->image) {
                    File::delete(public_path('assets/toko/image/' . self::getToko('kode_toko')) . '/' . $toko->image);
                }

                $file = $request->file('image');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $image = asset('assets/toko/image/' . self::getToko('kode_toko') . '/' . $fileName);
                $file->move(public_path('assets/toko/image/' . self::getToko('kode_toko')), $fileName);
                $toko->image = $image;
                $toko->save();

                return response()->json([
                    'status' => true,
                    'error' => false,
                    'message' => 'Berhasil Upload Foto.',
                    'image' => $image
                ], 200);
            }
        } catch (\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function updateSettingsToko(Request $request, $kodetoko) {
        $request->validate([
            'nama_toko' => 'required',
            'alamat_toko' => 'required',
            'deskripsi_toko' => 'required'
        ]);

        try {

            $toko = DetailToko::where('kode_toko', $kodetoko)->first();

            if(empty($toko)) {

                Alert::warning('Gagal!', 'Mohon maaf, terjadi kesalahan, data toko anda tidak ditemukan, silahkan ulangi beberapa saat lagi.');

                return redirect()->back();
            }

            $toko->nama_toko = $request->nama_toko;
            $toko->alamat_toko = $request->alamat_toko;
            $toko->jam_buka = $request->jam_buka;
            $toko->jam_tutup = $request->jam_tutup;
            $toko->deskripsi_toko = $request->deskripsi_toko;
            $toko->save();


            Alert::success('Sukses!', 'Berhasil update profile toko.');
            return redirect()->back();

        }catch(\Exception $err) {
            return ErrorController::getError($err);
        }
    }
}
