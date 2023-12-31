<?php

use App\Models\User;
use App\Models\Saldo;
use App\Models\Message;
use App\Events\LiveChat;
use App\Events\TestLagi;
use App\Models\SaldoToko;
use App\Events\TestMessage;
use App\Http\Controllers\Admin\Artikel\ArtikelController;
use App\Jobs\SendInvoiceToko;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\FormController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\Admin\Role\RoleController;
use App\Http\Controllers\Admin\Toko\TokoController;
use App\Http\Controllers\Admin\User\UserController;
use App\Http\Controllers\Admin\Order\OrderController;
use App\Http\Controllers\Admin\IorPay\IorpayController;
use App\Http\Controllers\Admin\Produk\ProdukController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Admin\Menu\MasterMenuController;
use App\Http\Controllers\Admin\Menu\ParentMenuController;
use App\Http\Controllers\Admin\Notifikasi\NotifikasiController;
use App\Http\Controllers\Admin\Settings\SettingController;
use App\Http\Controllers\Admin\Transaksi\TransaksiController;
use App\Http\Controllers\Admin\Payemnt\AdminPaymentController;
use App\Http\Controllers\Admin\Pendapatan\PendapatanController;
use App\Http\Controllers\Admin\Permission\PermissionController;
use App\Http\Controllers\Admin\Transaksi\DetailSaldoController;
use App\Http\Controllers\Admin\Settings\SettingBannerController;
use App\Http\Controllers\Admin\Transaksi\Topup\TransaksiTopupController;
use App\Http\Controllers\Admin\Transaksi\Withdraw\TransaksiWithdrawController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
  return view('admin.auth.login');
})->name('login')->middleware('guest');

Route::post('autenticate', [LoginController::class, 'authenticate'])->name('authenticate');

Route::get('user/order/download/{token}/{uuid}', [DownloadController::class, 'download'])->name('download');

Route::prefix('admin/')->name('admin.')->middleware('auth')->group(function () {
  Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
  Route::post('logout', [UserController::class, 'logout'])->name('logout');
  // Route Produk
  // Route::prefix('produk/')->name('produk.')->group(function() {
  //   Route::get('', [ProdukController::class, 'index'])->name('index');
  //   Route::get('semua-produk', [ProdukController::class, 'all_produk'])->name('semua-produk');
  //   Route::put('konfirmasi/{kode_produk}', [ProdukController::class, 'konfirmasi'])->name('konfirmasi');
  //   Route::put('update', [ProdukController::class, 'update'])->name('update');
  //   Route::delete('destroy', [ProdukController::class, 'destroy'])->name('destroy');
  //   Route::get('view/{kode_produk}', [ProdukController::class, 'view_produk'])->name('view-produk');
  // });

  // Route User
  Route::prefix('user/')->name('user.')->middleware('permission:daftar-user')->group(function () {
    Route::get('', [UserController::class, 'index'])->name('index');
    Route::get('konfirmasi-user', [UserController::class, 'konfirmasi'])->name('konfirmasi-user');
    Route::put('konfirmasi/{uuid_user}', [UserController::class, 'konfirmasi_user'])->name('konfirmasi');
    Route::post('block-pengguna', [UserController::class, 'blockPengguna'])->name('block-pengguna');
    Route::post('buka-block', [UserController::class, 'bukaBlock'])->name('buka-block');
    Route::put('reject/{uuid_user}', [UserController::class, 'reject'])->name('reject');
    // Route::put('update', [UserController::class, 'update'])->name('update');
    // Route::delete('destroy', [UserController::class, 'destroy'])->name('destroy');
    Route::get('view/{uuid_user}', [UserController::class, 'view_user'])->name('view');
    Route::get('daftar-user', [UserController::class, 'daftarUser'])->name('daftar-user');
  });

  // Route Toko
  Route::prefix('toko/')->name('toko.')->group(function () {
    Route::get('', [TokoController::class, 'index'])->name('index');
    Route::get('konfirmasi-toko', [TokoController::class, 'konfirmasi'])->name('konfirmasi-toko');
    Route::post('konfirmasi', [TokoController::class, 'konfirmasi_toko'])->name('konfirmasi');
    Route::post('data-konfirmasi', [TokoController::class, 'data_konfirmasi'])->name('data-konfirmasi');
    Route::post('data-toko', [TokoController::class, 'data_toko'])->name('data-toko');
    Route::put('batal-konfirmasi/{kode_toko}', [TokoController::class, 'batal_konfirmasi'])->name('batal-konfirmasi');
    Route::put('reject/{kode_toko}', [TokoController::class, 'reject'])->name('reject');
    // Route::put('update', [TokoController::class, 'update'])->name('update');
    // Route::delete('destroy', [TokoController::class, 'destroy'])->name('destroy');
    Route::get('view/{kode_toko}', [TokoController::class, 'view_toko'])->name('view');
  });

  // Route Produk
  Route::prefix('produk/')->name('produk.')->group(function () {
    Route::get('', [ProdukController::class, 'index'])->name('index');
    Route::get('semua-produk', [ProdukController::class, 'all_produk'])->middleware(['permission:show-produk'])->name('all-produk');
    Route::put('konfirmasi/{kode_produk}', [ProdukController::class, 'konfirmasi'])->name('konfirmasi');
    Route::put('update/{kode_produk}', [ProdukController::class, 'update_deskripsi'])->name('update');
    Route::delete('destroy', [ProdukController::class, 'destroy'])->name('destroy');
    Route::post('data-produk', [ProdukController::class, 'data_produk'])->name('data-produk');
    Route::get('view/{kode_produk}', [ProdukController::class, 'view_produk'])->name('view-produk');
  });

  // Route Payment
  Route::prefix('payment/')->name('payment.')->group(function () {
    Route::get('', [PaymentController::class, 'index'])->name('index');
    Route::post('konfirmasi', [PaymentController::class, 'konfirmasi'])->name('konfirmasi');
    Route::get('detail/{no_order}', [PaymentController::class, 'detail'])->name('detail');

    // ADMIN
    Route::get('daftar-payment', [AdminPaymentController::class, 'daftar_payment'])->name('daftar-payment');
    Route::post('data-payment', [AdminPaymentController::class, 'data_payment'])->name('data-payment');
    Route::post('store', [AdminPaymentController::class, 'store'])->name('store');
    Route::post('edit', [AdminPaymentController::class, 'edit'])->name('edit');
    Route::post('update', [AdminPaymentController::class, 'update'])->name('update');
    Route::post('destroy', [AdminPaymentController::class, 'destroy'])->name('destroy');
    // DATA
    Route::post('konfirmasi-payment-data', [PaymentController::class, 'konfirmasi_data'])->name('konfirmasi-data');
  });

  // Route Iorpay
  Route::prefix('iorpay/')->name('iorpay.')->group(function () {
    Route::get('permintaan-topup', [IorpayController::class, 'permintaan_topup'])->middleware(['permission:permintaan-topup'])->name('permintaan-topup');
    Route::get('permintaan-withdraw', [IorpayController::class, 'permintaan_withdraw'])->middleware(['permission:permintaan-withdraw'])->name('permintaan-withdraw');
    Route::get('daftar-pengguna', [IorpayController::class, 'daftar_pengguna'])->name('daftar_pengguna');
    Route::post('konfirmasi', [IorpayController::class, 'konfirmasi'])->middleware(['permission:konfirmasi-topup'])->name('konfirmasi-topup');
    Route::post('konfirmasi-topup-data', [IorpayController::class, 'konfirmasi_topup_data'])->name('konfirmasi-topup-data');
    Route::put('konfirmasi-withdraw/{no_trx}', [IorpayController::class, 'konfirmasi_withdraw'])->middleware(['permission:konfirmasi-withdraw'])->name('konfirmasi-withdraw');
    Route::get('detail/{no_order}', [IorpayController::class, 'detail'])->name('detail');
  });

  // Route Transaksi
  Route::prefix('transaksi/')->name('transaksi.')->group(function () {
    Route::get('', [TransaksiController::class, 'index'])->middleware('permission:show-transaksi')->name('index');
    Route::get('detail/{no_order}', [TransaksiController::class, 'detail'])->name('detail');
    Route::post('data-transaksi', [TransaksiController::class, 'data_transaksi'])->name('data-transaksi');
    Route::post('data-transaksi-produk', [TransaksiController::class, 'data_transaksi_produk'])->name('data-transaksi-produk');

    Route::prefix('withdraw/')->name('withdraw.')->group(function () {
      Route::get('', [TransaksiWithdrawController::class, 'index'])->name('index');
      Route::post('data-withdraw', [TransaksiWithdrawController::class, 'data_withdraw'])->name('data-withdraw');
    });

    Route::prefix('topup/')->name('topup.')->group(function () {
      Route::get('', [TransaksiTopupController::class, 'index'])->name('index');
      Route::post('data-topup', [TransaksiTopupController::class, 'data_topup'])->name('data-topup');
    });
  });

  // Route Order
  Route::prefix('order/')->name('order.')->group(function () {
    Route::get('', [OrderController::class, 'index'])->name('index');
    Route::put('konfirmasi/{no_order}', [OrderController::class, 'konfirmasi'])->name('konfirmasi');
    Route::get('detail/{no_order}', [OrderController::class, 'detail'])->name('detail');
    Route::get('daftar-order', [OrderController::class, 'daftar_order'])->name('daftar-order');
    Route::get('data-order', [OrderController::class, 'data_order'])->name('data-order');
    Route::get('detail-order/{no_order}', [OrderController::class, 'detail_order'])->name('detail-order');
  });

  Route::prefix('settings/')->name('settings.')->group(function () {
    Route::get('', [SettingController::class, 'index'])->name('index');
    Route::post('update', [SettingController::class, 'update'])->name('update');

    Route::prefix('banner/')->name('banner.')->middleware('permission:setting-banner')->group(function () {
      Route::get('', [SettingBannerController::class, 'index'])->name('index');
      Route::post('data-banner', [SettingBannerController::class, 'dataBanner'])->name('data-banner');
      Route::post('store', [SettingBannerController::class, 'store'])->name('store');
      Route::get('edit', [SettingBannerController::class, 'edit'])->name('edit');
      Route::post('update', [SettingBannerController::class, 'update'])->name('update');
      Route::post('destroy', [SettingBannerController::class, 'destroy'])->name('destroy');
    });
  });

  Route::prefix('notifikasi/')->name('notifikasi.')->group(function () {
    Route::get('', [NotifikasiController::class, 'index'])->name('index');
    Route::post('update', [NotifikasiController::class, 'update'])->name('update');
    Route::get('render-popup', [NotifikasiController::class, 'renderViewPopup'])->name('render-popup');
    Route::get('render-bartop', [NotifikasiController::class, 'renderViewBartop'])->name('render-bartop');
  });

  Route::get('pendapatan', [PendapatanController::class, 'index'])->name('pendapatan');
  Route::get('bank/detail-saldo', [DetailSaldoController::class, 'index'])->name('detail-saldo');
  Route::post('bank/tarik-dana', [DetailSaldoController::class, 'penarikan_dana'])->name('tarik-dana');

  Route::prefix('role/')->name('role.')->group(function () {
    Route::get('', [RoleController::class, 'index'])->name('index');
    Route::get('create-role', [RoleController::class, 'create'])->name('create');
    Route::post('data-role', [RoleController::class, 'data_role'])->name('data-role');
    Route::post('edit', [RoleController::class, 'edit'])->name('edit');
    Route::post('store', [RoleController::class, 'store'])->name('store');
    Route::post('update', [RoleController::class, 'update'])->name('update');
    Route::post('destroy', [RoleController::class, 'destroy'])->name('destroy');
  });

  Route::prefix('permission/')->name('permission.')->group(function () {
    Route::get('', [PermissionController::class, 'index'])->name('index');
    Route::post('data-permission', [PermissionController::class, 'data_permission'])->name('data-permission');
    Route::post('edit', [PermissionController::class, 'edit'])->name('edit');
    Route::post('store', [PermissionController::class, 'store'])->name('store');
    Route::post('update', [PermissionController::class, 'update'])->name('update');
    Route::post('destroy', [PermissionController::class, 'destroy'])->name('destroy');
  });

  Route::prefix('ms-menu/')->name('ms-menu.')->group(function () {
    Route::get('', [MasterMenuController::class, 'index'])->middleware(['permission:show-menu'])->name('index');
    Route::post('data-menu', [MasterMenuController::class, 'data_menu'])->name('data-menu');
    Route::get('setting/{id}', [MasterMenuController::class, 'setting'])->name('setting');
    Route::get('create', [MasterMenuController::class, 'create'])->middleware(['permission:create-menu'])->name('create');
    Route::post('edit', [MasterMenuController::class, 'edit'])->name('edit');
    Route::post('store', [MasterMenuController::class, 'store'])->name('store');
    Route::post('update/{id_menu}', [MasterMenuController::class, 'update'])->name('update');
    Route::post('destroy', [MasterMenuController::class, 'destroy'])->name('destroy');

    // Parent Menu
    Route::prefix('parent/')->name('parent.')->middleware(['permission:parent-menu'])->group(function () {
      Route::get('', [ParentMenuController::class, 'index'])->name('index');
      Route::post('store', [ParentMenuController::class, 'store'])->name('store');
    });
  });

  // Route Artikel
  Route::prefix('artikel/')->middleware(['permission:artikel'])->name('artikel.')->group(function () {
    Route::get('', [ArtikelController::class, 'index'])->name('index');
    Route::get('create', [ArtikelController::class, 'create'])->name('create');
    Route::post('data-artikel', [ArtikelController::class, 'dataArtikel'])->name('data-artikel');
    Route::post('store', [ArtikelController::class, 'store'])->name('store');
    Route::post('store-kategori', [ArtikelController::class, 'storeKategori'])->name('store-kategori');
    Route::get('view-kategori', [ArtikelController::class, 'viewKategori'])->name('view-kategori');
    Route::get('edit/{slug}', [ArtikelController::class, 'edit'])->name('edit');
    Route::post('update/{slug}', [ArtikelController::class, 'update'])->name('update');
    Route::post('destroy', [ArtikelController::class, 'destroy'])->name('destroy');
  });
});
