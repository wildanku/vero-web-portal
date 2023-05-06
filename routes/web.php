<?php

use App\Http\Controllers\DatasetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('main');
});

Route::prefix('ajax')->name('ajax.')->group(function() {
    Route::get('/data', [DatasetController::class, 'get'])->name('get');
    Route::get('/login', [DatasetController::class, 'login']);
});
