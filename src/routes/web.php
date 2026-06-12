<?php

use App\Http\Controllers\TopController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// Laravelのトップページ
Route::get('/', [TopController::class, 'index'])
    ->name('top');

