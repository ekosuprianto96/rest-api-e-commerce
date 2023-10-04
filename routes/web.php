<?php

use App\Models\User;
use App\Models\Saldo;
use App\Models\Message;
use App\Events\LiveChat;
use App\Events\TestLagi;
use App\Models\SaldoToko;
use App\Events\TestMessage;
use App\Http\Controllers\Admin\IorPay\IorpayController;
use App\Jobs\SendInvoiceToko;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\Order\OrderController;
use App\Http\Controllers\Admin\Toko\TokoController;
use App\Http\Controllers\Admin\User\UserController;
use App\Http\Controllers\Admin\Produk\ProdukController;
use App\Http\Controllers\API\FormController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\Dashboard\DashboardController;

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

Route::get('/', function() {
  return view('admin.auth.login');
})->name('dashboard');
Route::post('autenticate', [LoginController::class, 'authenticate'])->name('authenticate');

Route::get('user/order/download', [DownloadController::class, 'download'])->name('download');

Route::prefix('admin/')->name('admin.')->group(function() {
  Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

  // Route Produk
  Route::prefix('produk/')->name('produk.')->group(function() {
    Route::get('', [ProdukController::class, 'index'])->name('index');
    Route::get('semua-produk', [ProdukController::class, 'all_produk'])->name('all-produk');
    Route::put('konfirmasi/{kode_produk}', [ProdukController::class, 'konfirmasi'])->name('konfirmasi');
    Route::put('update', [ProdukController::class, 'update'])->name('update');
    Route::delete('destroy', [ProdukController::class, 'destroy'])->name('destroy');
    Route::get('view/{kode_produk}', [ProdukController::class, 'view_produk'])->name('view-produk');
  });

  // Route User
  Route::prefix('user/')->name('user.')->group(function() {
    Route::get('', [UserController::class, 'index'])->name('index');
    Route::get('konfirmasi-user', [UserController::class, 'konfirmasi'])->name('konfirmasi-user');
    Route::put('konfirmasi/{uuid_user}', [UserController::class, 'konfirmasi_user'])->name('konfirmasi');
    Route::put('batal-konfirmasi/{uuid_user}', [UserController::class, 'batal_konfirmasi'])->name('batal-konfirmasi');
    Route::put('reject/{uuid_user}', [UserController::class, 'reject'])->name('reject');
    // Route::put('update', [UserController::class, 'update'])->name('update');
    // Route::delete('destroy', [UserController::class, 'destroy'])->name('destroy');
    Route::get('view/{uuid_user}', [UserController::class, 'view_user'])->name('view');
  });

  // Route Toko
  Route::prefix('toko/')->name('toko.')->group(function() {
    Route::get('', [TokoController::class, 'index'])->name('index');
    Route::get('konfirmasi-toko', [TokoController::class, 'konfirmasi'])->name('konfirmasi-toko');
    Route::put('konfirmasi/{kode_toko}', [TokoController::class, 'konfirmasi_toko'])->name('konfirmasi');
    Route::put('batal-konfirmasi/{kode_toko}', [TokoController::class, 'batal_konfirmasi'])->name('batal-konfirmasi');
    Route::put('reject/{kode_toko}', [TokoController::class, 'reject'])->name('reject');
    // Route::put('update', [TokoController::class, 'update'])->name('update');
    // Route::delete('destroy', [TokoController::class, 'destroy'])->name('destroy');
    Route::get('view/{kode_toko}', [TokoController::class, 'view_user'])->name('view');
  });

  // Route Produk
  Route::prefix('produk/')->name('produk.')->group(function() {
    Route::get('', [ProdukController::class, 'index'])->name('index');
    Route::get('semua-produk', [ProdukController::class, 'all_produk'])->name('all-produk');
    Route::put('konfirmasi/{kode_produk}', [ProdukController::class, 'konfirmasi'])->name('konfirmasi');
    Route::put('update', [ProdukController::class, 'update'])->name('update');
    Route::delete('destroy', [ProdukController::class, 'destroy'])->name('destroy');
    Route::get('view/{kode_produk}', [ProdukController::class, 'view_produk'])->name('view-produk');
  });

  // Route Payment
  Route::prefix('payment/')->name('payment.')->group(function() {
    Route::get('', [PaymentController::class, 'index'])->name('index');
    Route::put('konfirmasi/{no_order}', [PaymentController::class, 'konfirmasi'])->name('konfirmasi');
    Route::get('detail/{no_order}', [PaymentController::class, 'detail'])->name('detail');
  });

  // Route Iorpay
  Route::prefix('iorpay/')->name('iorpay.')->group(function() {
    Route::get('permintaan-topup', [IorpayController::class, 'permintaan_topup'])->name('permintaan-topup');
    Route::get('daftar-pengguna', [IorpayController::class, 'daftar_pengguna'])->name('daftar_pengguna');
    Route::put('konfirmasi/{no_trx}', [IorpayController::class, 'konfirmasi'])->name('konfirmasi');
    Route::get('detail/{no_order}', [IorpayController::class, 'detail'])->name('detail');
  });

  // Route Order
  Route::prefix('order/')->name('order.')->group(function() {
    Route::get('', [OrderController::class, 'index'])->name('index');
    Route::put('konfirmasi/{no_order}', [OrderController::class, 'konfirmasi'])->name('konfirmasi');
    Route::get('detail/{no_order}', [OrderController::class, 'detail'])->name('detail');
  });
});
