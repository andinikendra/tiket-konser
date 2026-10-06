<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/event/{event:slug}', [EventController::class, 'show'])->name('events.show');

Route::prefix('checkout/{event:slug}')->name('checkout.')->group(function () {
    // Step 1 -> kirim pilihan tiket
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
    // Step 2 -> Detail Pesanan
    Route::get('detail', [CheckoutController::class, 'detail'])->name('detail');
    Route::post('detail', [CheckoutController::class, 'saveDetail'])->name('detail.save');
    // Step 3 -> Metode Pembayaran
    Route::get('metode', [CheckoutController::class, 'method'])->name('method');
    Route::post('metode', [CheckoutController::class, 'saveMethod'])->name('method.save');
    // Step 4 -> Pembayaran
    Route::get('bayar', [CheckoutController::class, 'payment'])->name('payment');
});
