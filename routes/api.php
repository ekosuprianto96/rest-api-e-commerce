<?php

use App\Models\IorPay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\FormController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\IorPayController;
use App\Http\Controllers\API\ProdukController;
use App\Http\Controllers\API\SocketController;
use App\Http\Controllers\DetailTokoController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\CheckoutController;
use App\Http\Controllers\API\KategoriController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\WishListController;
use App\Http\Controllers\API\AuthenticateController;
use App\Http\Controllers\API\RegisterTokoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('checkout')->middleware('auth:api')->group(function() {
    Route::post('', [CheckoutController::class, 'checkout']);
});

Route::post('midtrans/notification', [CheckoutController::class, 'notification'])->name('notification');
Route::post('login', [AuthenticateController::class, 'authenticate']);
Route::post('register', [RegisterController::class, 'store']);

Route::post('produk/show', [ProdukController::class, 'show'])->name('show');
Route::get('kategori/show', [KategoriController::class, 'show'])->name('show');

Route::middleware('auth:api')->get('authenticate', function (Request $request) {
    $iorpay = IorPay::where('uuid_user', auth()->guard('api')->user()->uuid)->first();
    $iorpay->saldo = $iorpay->saldo_formatted;
    return response()->json([
        'status' => true,
        'error' => false,
        'user' => $request->user(),
        'toko' => auth()->guard('api')->user()->toko,
        'wishlist' => auth()->guard('api')->user()->wishlist,
        'carts' => auth()->guard('api')->user()->cart,
        'iorpay' => $iorpay,
        'meesage' => 'Get data success fully.'
    ]);
});
Route::prefix('toko/')->middleware('auth:api')->name('toko.')->group(function() {
    Route::post('register', [RegisterTokoController::class, 'store'])->name('register');
    Route::prefix('produk/')->name('produk.')->group(function() {
        Route::post('upload-produk', [ProdukController::class, 'store'])->name('upload-produk');
    });

});

Route::prefix('payment/')->name('payment.')->group(function() {
    Route::get('show', [PaymentController::class, 'show'])->name('show');
});

Route::prefix('user/')->name('user.')->middleware('auth:api')->group(function() {
    Route::post('logout', [AuthenticateController::class, 'logout'])->name('logout');
    Route::post('update', [AuthenticateController::class, 'update'])->name('update');
    Route::post('upload-image', [AuthenticateController::class, 'upload_image'])->name('upload-image');
    // Chat
    Route::prefix('chat/')->name('chat.')->group(function() {
        Route::get('', [MessageController::class, 'index'])->name('index');
        Route::post('store', [MessageController::class, 'store'])->name('store');
    });

    // Iorpay
    Route::prefix('iorpay/')->name('iorpay.')->group(function() {
        Route::post('get-trx', [IorPayController::class, 'get_trx'])->name('get-trx');
        Route::post('top-up', [IorPayController::class, 'top_up'])->name('top-up');
        Route::post('refresh', [IorPayController::class, 'refresh'])->name('refresh');
    });

    Route::prefix('cart/')->name('cart.')->group(function() {
        Route::get('show', [CartController::class, 'show'])->name('show');
        Route::post('destroy', [CartController::class, 'destroy'])->name('destroy');
        Route::post('store', [CartController::class, 'store'])->name('store');
    });

    Route::prefix('wishlist/')->name('wishlist.')->group(function() {
        Route::get('show', [WishListController::class, 'show'])->name('show');
        Route::post('destroy', [WishListController::class, 'destroy'])->name('destroy');
        Route::post('store', [WishListController::class, 'store'])->name('store');
    });

    Route::prefix('order/')->name('order.')->group(function() {
        Route::post('show', [OrderController::class, 'show'])->name('show');
        Route::post('konfirmasi', [OrderController::class, 'konfirmasi'])->name('konfirmasi');
        Route::post('akses', [OrderController::class, 'akses'])->name('akses');
        Route::post('download', [OrderController::class, 'download'])->name('download');
        Route::post('checkout-manual', [OrderController::class, 'checkout_manual'])->name('checkout-manual');
        Route::post('checkout', [OrderController::class, 'checkout'])->name('checkout');
        Route::post('cancel', [OrderController::class, 'cancel'])->name('cancel');
        Route::post('store', [OrderController::class, 'store'])->name('store');
    });

    Route::prefix('toko/')->name('toko.')->group(function() {
        Route::get('detail', [DetailTokoController::class, 'detail'])->name('detail');
        Route::get('order/{kode_toko}', [DetailTokoController::class, 'order'])->name('order');
        Route::post('update', [DetailTokoController::class, 'update'])->name('update');
        Route::post('upload-image', [DetailTokoController::class, 'upload_image'])->name('upload-image');
        Route::post('checkout', [DetailTokoController::class, 'checkout'])->name('checkout');
        Route::post('destroy', [DetailTokoController::class, 'destroy'])->name('destroy');
        Route::post('store', [DetailTokoController::class, 'store'])->name('store');
    });

    // Route Form
    Route::prefix('form/')->name('form.')->group(function() {
        Route::post('send', [FormController::class, 'store'])->name('store');
    });
});