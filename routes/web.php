<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\ClipInController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PreOrderController;
use App\Http\Controllers\TextureController;
use App\Http\Controllers\TrackController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/textures', [TextureController::class, 'index'])->name('textures.index');
Route::get('/textures/{texture}', [TextureController::class, 'show'])->name('textures.show');
Route::get('/texture-match', [PageController::class, 'match'])->name('match');
Route::get('/clip-ins', [ClipInController::class, 'show'])->name('clipins');
Route::post('/clip-ins', [ClipInController::class, 'store'])->middleware('throttle:requests')->name('clipins.store');
Route::get('/queen-cam', [PageController::class, 'gallery'])->name('gallery');
Route::get('/care', [PageController::class, 'care'])->name('care');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/our-story', [PageController::class, 'story'])->name('story');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');

Route::get('/pre-order', [PreOrderController::class, 'create'])->name('preorder');
Route::post('/pre-order', [PreOrderController::class, 'store'])->middleware('throttle:requests')->name('preorder.store');
Route::get('/pre-order/sent/{preOrder}', [PreOrderController::class, 'sent'])->name('preorder.sent');

Route::get('/track', [TrackController::class, 'form'])->name('track');
Route::post('/track', [TrackController::class, 'lookup'])->middleware('throttle:tracking')->name('track.lookup');
Route::get('/track/{preOrder}', [TrackController::class, 'show'])->name('track.show');

Route::get('/sitemap.xml', [PageController::class, 'sitemap'])->name('sitemap');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [Admin\AuthController::class, 'form'])->middleware('guest')->name('login');
    Route::post('/login', [Admin\AuthController::class, 'login'])->middleware(['guest', 'throttle:login'])->name('login.attempt');

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/requests', [Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('/requests/{preOrder}', [Admin\OrderController::class, 'show'])->name('orders.show');
        Route::post('/requests/{preOrder}/status', [Admin\OrderController::class, 'status'])->name('orders.status');
        Route::patch('/requests/{preOrder}', [Admin\OrderController::class, 'update'])->name('orders.update');

        Route::get('/prices', [Admin\PriceController::class, 'index'])->name('prices.index');
        Route::get('/prices/{texture}', [Admin\PriceController::class, 'edit'])->name('prices.edit');
        Route::put('/prices/{texture}', [Admin\PriceController::class, 'update'])->name('prices.update');
        Route::post('/prices/{texture}/sets', [Admin\PriceController::class, 'add'])->name('prices.add');

        Route::get('/photos', [Admin\PhotoController::class, 'index'])->name('photos.index');
        Route::post('/photos', [Admin\PhotoController::class, 'store'])->name('photos.store');
        Route::patch('/photos/{photo}', [Admin\PhotoController::class, 'update'])->name('photos.update');
        Route::delete('/photos/{photo}', [Admin\PhotoController::class, 'destroy'])->middleware('can:owner')->name('photos.destroy');

        Route::middleware('can:owner')->group(function () {
            Route::get('/settings', [Admin\SettingsController::class, 'edit'])->name('settings');
            Route::put('/settings', [Admin\SettingsController::class, 'update'])->name('settings.update');
            Route::get('/team', [Admin\TeamController::class, 'index'])->name('team');
            Route::post('/team', [Admin\TeamController::class, 'store'])->name('team.store');
            Route::delete('/team/{user}', [Admin\TeamController::class, 'destroy'])->name('team.destroy');
        });
    });
});
