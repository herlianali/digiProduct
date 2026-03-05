<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\CompanySlidersController;
use App\Http\Controllers\Admin\ContentSettingController;
use App\Http\Controllers\Admin\HomeSlidersController;
use App\Http\Controllers\Admin\OrderController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\TestimonialsController;
use App\Http\Controllers\LandingController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::get('/', [LandingController::class, 'index']);

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('articles', ArticleController::class);

    Route::prefix('settings')->group(function () {
        Route::resource('slider-company', CompanySlidersController::class);
        Route::resource('content', ContentSettingController::class);
        Route::resource('slider-home', HomeSlidersController::class);
        Route::resource('testimoni-user', TestimonialsController::class);
    });

});
