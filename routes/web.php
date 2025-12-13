<?php

use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QrController;
use App\Http\Controllers\RedirectController;
use App\Models\Plans;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/r/{slug}', [RedirectController::class, 'redirect'])
    ->name('link.redirect');

Route::get('/qr/{slug}', [QrController::class, 'generate'])
    ->name('link.qr');

Route::get('/admin/pricing', [CheckoutController::class, 'index'])->name('pricing.index');
Route::post('billing/pay', [CheckoutController::class, 'pay'])->name('billing.pay');
