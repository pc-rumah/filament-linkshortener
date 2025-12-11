<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QrController;
use App\Http\Controllers\RedirectController;
use App\Http\Middleware\Ceklimit;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/r/{slug}', [RedirectController::class, 'redirect'])
    ->name('link.redirect');

Route::get('/qr/{slug}', [QrController::class, 'generate'])
    ->name('link.qr');
