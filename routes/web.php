<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| POS System – Toko Fenny Grosir
| Redirect semua request ke aplikasi POS di Laragon
|--------------------------------------------------------------------------
*/

// Root → langsung redirect ke halaman login POS
Route::get('/', function () {
    return redirect('http://localhost/SkripsiFibril/pos_system/modules/auth/login.php');
});

// Fallback: semua route lain juga redirect ke POS
Route::fallback(function () {
    return redirect('http://localhost/SkripsiFibril/pos_system/modules/auth/login.php');
});
