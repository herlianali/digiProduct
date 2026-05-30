<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ─── Auth ─────────────────────────────────────────────────────────
use App\Http\Controllers\Auth\LoginController;

// ─── Public ───────────────────────────────────────────────────────
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProductPlacementController;

// ─── Admin ────────────────────────────────────────────────────────
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\CatalogController;
use App\Http\Controllers\Admin\ContentSettingController;
use App\Http\Controllers\Admin\TestimonialsController;
use App\Http\Controllers\Admin\HomeSlidersController;
use App\Http\Controllers\Admin\CompanySlidersController;

// ══════════════════════════════════════════════════════════════════
// GUEST
// ══════════════════════════════════════════════════════════════════

Route::middleware('guest')->group(function () {
    Route::get ('/login', [LoginController::class, 'show'] )->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// ══════════════════════════════════════════════════════════════════
// PUBLIC
// ══════════════════════════════════════════════════════════════════

Route::get('/',                         [LandingController::class, 'index']);
Route::get('/gsap',                     [LandingController::class, 'gsap'])->name('gsap');
Route::get('/product-place',            [ProductPlacementController::class, 'index'])->name('ProductPlace.index');
Route::get('/product-place/detail/{id}',[ProductPlacementController::class, 'show'])->name('ProductPlace.show');
Route::get('/get-in-touch',             [LandingController::class, 'GetInTuch'])->name('GetInTuch');
Route::get('/our-team',                 [LandingController::class, 'OurTeam'])->name('OurTeam');
Route::get('/portfolio/detail/{id}',    [LandingController::class, 'PortfolioDetail'])->name('PortfolioDetail');

// ══════════════════════════════════════════════════════════════════
// ADMIN
// ══════════════════════════════════════════════════════════════════

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {

    // ── Auth ──────────────────────────────────────────────────────
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // ── Dashboard ─────────────────────────────────────────────────
    Route::get('/dashboard', fn() => Inertia::render('Dashboard'))->name('dashboard');

    // ── Core Resources ────────────────────────────────────────────
    Route::resource('orders',   OrderController::class);
    Route::resource('articles', ArticleController::class);
    Route::prefix('products')->name('products.')->middleware(['auth'])->group(function () {
        Route::get('/',                         [ProductController::class, 'index'])->name('index');
        Route::get('/create',                   [ProductController::class, 'create'])->name('create');
        Route::post('/',                        [ProductController::class, 'store'])->name('store');
        Route::get('/{product}',                [ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit',           [ProductController::class, 'edit'])->name('edit');
        Route::post('/{product}',               [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}',             [ProductController::class, 'destroy'])->name('destroy');
        Route::post('{product}/reorder-images', [ProductController::class, 'reorderImages'])->name('reorder-images');
    });
    
    // ── Admin: Catalog (styles, tags, tag groups) ─────────────────────
    Route::prefix('catalog')->name('catalog.')->middleware(['auth'])->group(function () {
        Route::get('/', [CatalogController::class, 'index'])->name('index');
    
        // Styles
        Route::post('/styles',          [CatalogController::class, 'storeStyle'])->name('styles.store');
        Route::put('/styles/{style}',   [CatalogController::class, 'updateStyle'])->name('styles.update');
        Route::delete('/styles/{style}',[CatalogController::class, 'destroyStyle'])->name('styles.destroy');
    
        // Product categories
        Route::post('/categories',              [CatalogController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{category}',    [CatalogController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{category}', [CatalogController::class, 'destroyCategory'])->name('categories.destroy');
    
        // Tag groups
        Route::post('/tag-groups',               [CatalogController::class, 'storeTagGroup'])->name('tag-groups.store');
        Route::put('/tag-groups/{tagGroup}',     [CatalogController::class, 'updateTagGroup'])->name('tag-groups.update');
        Route::delete('/tag-groups/{tagGroup}',  [CatalogController::class, 'destroyTagGroup'])->name('tag-groups.destroy');
    
        // Tags
        Route::post('/tags',        [CatalogController::class, 'storeTag'])->name('tags.store');
        Route::put('/tags/{tag}',   [CatalogController::class, 'updateTag'])->name('tags.update');
        Route::delete('/tags/{tag}',[CatalogController::class, 'destroyTag'])->name('tags.destroy');
    });

    // ── Settings ──────────────────────────────────────────────────
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::resource('slider-home',    HomeSlidersController::class);
        Route::resource('slider-company', CompanySlidersController::class);
        Route::resource('content',        ContentSettingController::class);
        Route::resource('testimonials',   TestimonialsController::class);
    });
});