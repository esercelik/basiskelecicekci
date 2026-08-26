<?php

use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/urunler', [StorefrontController::class, 'index'])->name('products.index');
Route::get('/kategori/{category:slug}', [StorefrontController::class, 'category'])->name('categories.show');
Route::get('/urun/{product:slug}', [StorefrontController::class, 'show'])->name('products.show');
Route::get('/iletisim', [StorefrontController::class, 'contact'])->name('contact');
Route::get('/sitemap.xml', [StorefrontController::class, 'sitemap'])->name('sitemap');
