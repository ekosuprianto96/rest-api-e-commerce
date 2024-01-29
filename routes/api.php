<?php

use App\Http\Controllers\Admin\Settings\SettingController;
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
use App\Http\Controllers\API\BannerController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\ManifestController;
use App\Http\Controllers\API\PesananController;
use App\Http\Controllers\API\RegisterTokoController;
use App\Http\Controllers\API\SettingWebsiteController;
use App\Models\DetailToko;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('get-manifest', [ManifestController::class, 'index'])->name('manifest');
Route::prefix('checkout')->middleware('auth:api')->group(function () {
    Route::post('', [CheckoutController::class, 'checkout']);
});
Route::post('send-link', [ForgotPasswordController::class, 'sendLinkResetPassword'])->name('send-link');
Route::post('resend-email-konfirmasi', [ForgotPasswordController::class, 'resendingEmail'])->name('resend-email-konfirmasi');
Route::post('konfirmasi-email', [ForgotPasswordController::class, 'konfirmasiEmail'])->name('konfirmasi-email');
Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('reset-password');
Route::post('midtrans/notification', [CheckoutController::class, 'notification'])->name('notification');
Route::post('login', [AuthenticateController::class, 'authenticate']);
Route::post('register', [RegisterController::class, 'store']);

Route::post('produk/show', [ProdukController::class, 'show'])->name('show');
Route::get('produk-toko/{kode_toko}', [ProdukController::class, 'produk_toko'])->name('produk-toko');
Route::get('produk-serupa/{kategori}', [ProdukController::class, 'produk_serupa'])->name('produk-serupa');
Route::get('kategori/show', [KategoriController::class, 'show'])->name('show');
Route::get('settings', [SettingController::class, 'get_settings'])->name('settings');

Route::middleware('auth:api')->get('authenticate', function (Request $request) {
    $iorpay = IorPay::where('uuid_user', auth()->guard('api')->user()->uuid)->first();
    $iorpay->saldo = number_format($iorpay->saldo, 0);
    return response()->json([
        'status' => true,
        'error' => false,
        'user' => $request->user(),
        'wishlist' => auth()->guard('api')->user()->wishlist,
        'carts' => auth()->guard('api')->user()->cart,
        'pemberitahuan' => auth()->guard('api')->user()->pemberitahuan,
        'toko' => auth()->guard('api')->user()->toko,
        'iorpay' => $iorpay,
        'meesage' => 'Get data success fully.'
    ]);
});
Route::prefix('toko/')->middleware('auth:api')->name('toko.')->group(function () {
    Route::post('register', [RegisterTokoController::class, 'store'])->name('register');
    Route::prefix('produk/')->name('produk.')->group(function () {
        Route::post('upload-produk', [ProdukController::class, 'store'])->name('upload-produk');
    });
});

Route::prefix('payment/')->name('payment.')->group(function () {
    Route::get('show', [PaymentController::class, 'show'])->name('show');
});

// Route Setting
Route::prefix('settings/')->name('settings.')->group(function () {
    Route::get('banner', [BannerController::class, 'getBanner'])->name('banner');
    Route::get('settings', [SettingWebsiteController::class, 'getSettings'])->name('settings');
});

Route::prefix('user/')->name('user.')->middleware('auth:api')->group(function () {
    Route::post('logout', [AuthenticateController::class, 'logout'])->name('logout');
    Route::post('update', [AuthenticateController::class, 'update'])->name('update');
    Route::post('upload-image', [AuthenticateController::class, 'upload_image'])->name('upload-image');
    Route::get('komisi', [AuthenticateController::class, 'komisi'])->name('komisi');
    Route::get('notifikasi', [AuthenticateController::class, 'notifikasi'])->name('notifikasi');
    // Chat
    Route::prefix('chat/')->name('chat.')->group(function () {
        Route::post('', [MessageController::class, 'index'])->name('index');
        Route::post('store', [MessageController::class, 'store'])->name('store');
    });

    // Message
    Route::post('open-message', [MessageController::class, 'open'])->name('open-message');

    // Iorpay
    Route::prefix('iorpay/')->name('iorpay.')->group(function () {
        Route::post('get-trx', [IorPayController::class, 'get_trx'])->name('get-trx');
        Route::post('top-up', [IorPayController::class, 'top_up'])->name('top-up');
        Route::post('refresh', [IorPayController::class, 'refresh'])->name('refresh');
        Route::post('get-pay', [IorPayController::class, 'getIorPay'])->name('get-pay');
        Route::post('withdraw', [IorPayController::class, 'withdraw'])->name('withdraw');
        Route::post('detail-withdraw', [IorPayController::class, 'detailWithdraw'])->name('detail-withdraw');
    });

    Route::prefix('cart/')->name('cart.')->group(function () {
        Route::get('show', [CartController::class, 'show'])->name('show');
        Route::post('destroy', [CartController::class, 'destroy'])->name('destroy');
        Route::post('store', [CartController::class, 'store'])->name('store');
    });

    Route::prefix('wishlist/')->name('wishlist.')->group(function () {
        Route::get('show', [WishListController::class, 'show'])->name('show');
        Route::post('destroy', [WishListController::class, 'destroy'])->name('destroy');
        Route::post('store', [WishListController::class, 'store'])->name('store');
    });

    Route::prefix('order/')->name('order.')->group(function () {
        Route::post('show', [OrderController::class, 'show'])->name('show');
        Route::post('konfirmasi', [OrderController::class, 'konfirmasi'])->name('konfirmasi');
        Route::post('akses', [OrderController::class, 'akses'])->name('akses');
        Route::post('download', [OrderController::class, 'download'])->name('download');
        Route::post('checkout-manual', [OrderController::class, 'checkout_manual'])->name('checkout-manual');
        Route::post('checkout', [OrderController::class, 'checkout'])->name('checkout');
        Route::post('cancel', [OrderController::class, 'cancel'])->name('cancel');
        Route::post('store', [OrderController::class, 'store'])->name('store');
        Route::get('pesanan/{uuid_user}', [PesananController::class, 'pesanan'])->name('pesanan');
        Route::post('pesanan/konfirmasi', [PesananController::class, 'konfirmasi'])->name('konfirmasi');
        Route::post('detail-pesanan', [PesananController::class, 'detail_pesanan'])->name('detail-pesanan');
    });

    Route::prefix('toko/')->name('toko.')->group(function () {
        Route::get('detail', [DetailTokoController::class, 'detail'])->name('detail');
        Route::get('notifikasi', [DetailTokoController::class, 'notifikasi'])->name('notifikasi');
        Route::post('produk', [DetailTokoController::class, 'produk'])->name('produk');
        Route::post('produk/destroy', [DetailTokoController::class, 'destroy_produk'])->name('destroy-produk');
        Route::post('produk/edit', [DetailTokoController::class, 'edit'])->name('edit-produk');
        Route::post('produk/update', [DetailTokoController::class, 'produk_update'])->name('update-produk');
        Route::get('order/{kode_toko}', [DetailTokoController::class, 'order'])->name('order');
        Route::post('update', [DetailTokoController::class, 'update'])->name('update');
        Route::post('upload-image', [DetailTokoController::class, 'upload_image'])->name('upload-image');
        Route::post('checkout', [DetailTokoController::class, 'checkout'])->name('checkout');
        Route::post('destroy', [DetailTokoController::class, 'destroy'])->name('destroy');
        Route::post('store', [DetailTokoController::class, 'store'])->name('store');
        Route::get('message', [DetailTokoController::class, 'message'])->name('message');
        Route::post('detail-order', [DetailTokoController::class, 'detail_order'])->name('detail-order');
        Route::post('proses-order', [DetailTokoController::class, 'prosesOrder'])->name('proses-order');
        Route::post('message-toko', [MessageController::class, 'message_toko'])->name('message-toko');
        Route::post('store-toko', [MessageController::class, 'store_toko'])->name('store-toko');
        Route::post('open-message-toko', [MessageController::class, 'open_message_toko'])->name('open-message-toko');
        Route::post('order/upload-file', [DetailTokoController::class, 'upload_file'])->name('upload-file');
        Route::post('profile', [DetailTokoController::class, 'profile'])->name('profile');
    });

    // Route Form
    Route::prefix('form/')->name('form.')->group(function () {
        Route::post('send', [FormController::class, 'store'])->name('store');
    });
});
