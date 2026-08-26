<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\StoreSettingsController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/urunler', [StorefrontController::class, 'index'])->name('products.index');
Route::get('/kategori/{category:slug}', [StorefrontController::class, 'category'])->name('categories.show');
Route::get('/urun/{product:slug}', [StorefrontController::class, 'show'])->name('products.show');
Route::get('/iletisim', [StorefrontController::class, 'contact'])->name('contact');
Route::get('/sitemap.xml', [StorefrontController::class, 'sitemap'])->name('sitemap');

Route::prefix('admin')->as('admin.')->group(function (): void {
    Route::get('giris', [AuthController::class, 'create'])->middleware('guest')->name('login');
    Route::post('giris', [AuthController::class, 'store'])->middleware('guest')->name('login.store');

    Route::middleware(['auth', 'admin'])->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::post('cikis', [AuthController::class, 'destroy'])->name('logout');
        Route::resource('kategoriler', AdminCategoryController::class)->parameters(['kategoriler' => 'category'])->names('categories');
        Route::resource('urunler', AdminProductController::class)->parameters(['urunler' => 'product'])->names('products');
        Route::delete('urunler/{product}/gorseller/{image}', [ProductImageController::class, 'destroy'])->name('products.images.destroy');
        Route::get('magaza-ayarlari', [StoreSettingsController::class, 'edit'])->name('settings.edit');
        Route::put('magaza-ayarlari', [StoreSettingsController::class, 'update'])->name('settings.update');
        Route::get('profil', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profil', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('profil/parola', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    });
});
