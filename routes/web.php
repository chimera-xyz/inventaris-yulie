<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemHistoryController;
use App\Http\Controllers\PublicItemController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::get('/scan/{item:unique_code}', [PublicItemController::class, 'show'])->name('public.items.show');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');

    Route::resource('categories', CategoryController::class);

    Route::get('/items/export', [ItemController::class, 'export'])->name('items.export');
    Route::post('/items/export-selected', [ItemController::class, 'exportSelected'])->name('items.export-selected');
    Route::resource('items', ItemController::class);

    Route::get('/items/{item}/generate-qr', [ItemController::class, 'generateQRCode'])->name('items.generate-qr');
    Route::get('/items/{item}/print-qr', [ItemController::class, 'printQRCode'])->name('items.print-qr');
    Route::get('/items/{item}/print-code', [ItemController::class, 'printCode'])->name('items.print-code');
    Route::get('/items/{item}/print-label', [ItemController::class, 'printLabel'])->name('items.print-label');
    Route::post('/items/print-bulk-qr', [ItemController::class, 'printBulkQRCode'])->name('items.print-bulk-qr');

    Route::delete('/items/{item}/photos', [ItemController::class, 'destroyPhotos'])->name('items.photos.destroy');
    Route::post('/items/{item}/histories', [ItemHistoryController::class, 'store'])->name('items.histories.store');
    Route::put('/items/{item}/histories/{history}', [ItemHistoryController::class, 'update'])->name('items.histories.update');

    Route::get('/items/{item}/logs', [ItemController::class, 'logs'])->name('items.logs');
});
