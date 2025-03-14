<?php

use App\Http\Controllers\AddressHomeController;
use App\Http\Controllers\CartHomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutHomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FilepondController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductHomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaleController;
use App\Mail\ConfirmationPayment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/order/download-pdf/{id}', [OrderController::class, 'downloadPDF'])->name('order.download-pdf');

Route::get('/test-email', function () {
    // Data contoh
    $user = \App\Models\User::find(1); // Gantilah dengan user yang valid
    $paymentDetails = \App\Models\Order::with(['orderItems.product', 'orderItems.stock.size'])->find(1); // Gantilah dengan order yang valid

    Mail::to($user->email)->queue(new ConfirmationPayment($user, $paymentDetails));
    return 'Email sent!';
});

Route::get('/', [HomeController::class, 'index']);

Route::resource('product', ProductHomeController::class);
Route::resource('cart', CartHomeController::class)->middleware('auth');
Route::resource('checkout', CheckoutHomeController::class)->middleware('auth');
Route::resource('address', AddressHomeController::class);
Route::resource('feedback', FeedbackController::class);
Route::name('order.')->prefix('order')->group(function () {
    Route::post('/accept-delivered/{id}', [OrderController::class, 'acceptDelivered'])->name('accept-delivered');
    Route::post('/cancel-order/{id}', [OrderController::class, 'cancelOrder'])->name('cancel-order');
});

Route::name('dashboard.')->prefix('dashboard')->middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    Route::post('/upload-image', [FilepondController::class, 'uploadImage'])->name('upload-image');
    Route::delete('/cancel-image', [FilepondController::class, 'cancelImage'])->name('cancel-image');
    Route::post('/upload-image-multiple', [FilepondController::class, 'uploadImageMultiple'])->name('upload-image-multiple');
    Route::delete('/cancel-image-multiple', [FilepondController::class, 'cancelImageMultiple'])->name('cancel-image-multiple');

    Route::name('product.')->prefix('product')->group(function () {
        Route::get('/data', [ProductController::class, 'data'])->name('data');
        route::post('/image', [ProductController::class, 'uploadImage'])->name('image');
        Route::delete('/remove-image-multiple', [FilepondController::class, 'removeImageMultiple'])->name('remove-image-multiple');

        Route::name('category.')->prefix('category')->group(function () {
            Route::get('/data', [CategoryController::class, 'data'])->name('data');
        });
        Route::resource('category', CategoryController::class);
    });
    Route::resource('product', ProductController::class);


    Route::name('sale.')->prefix('sale')->group(function () {
        Route::get('/data', [SaleController::class, 'data'])->name('data');
    });
    Route::resource('sale', SaleController::class);
});

Route::name('profile.')->prefix('profile')->middleware(['auth'])->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    Route::post('/image', [ProfileController::class, 'updateImage'])->name('image');
});

require __DIR__ . '/auth.php';
