<?php

use App\Http\Controllers\Functions;
use Illuminate\Support\Facades\Route;


Route::get('/', [Functions::class, 'getAgreementFile'])->name('getAgreementFile');



// Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
