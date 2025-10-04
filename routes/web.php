<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PagesController;
use Illuminate\Support\Facades\Auth;
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

// Route::get('/', function () {
//     return view('welcome');
// });


Route::prefix('/')->controller(PagesController::class)->group(function(){
    Route::get('', 'index')->name('index');
    Route::get('dashboard', 'dashboard')->name('dashboard');
    Route::get('gift-store', 'giftStore')->name('gift-store');
    Route::get('instrument-rental', 'instrumentRental')->name('instrument-rental');
    Route::get('music-studio', 'musicStudio')->name('music-studio');
    Route::get('reports', 'reports')->name('reports');
    Route::get('settings', 'settings')->name('settings');
    Route::get('supermarket', 'supermarket')->name('supermarket');
    Route::get('test', 'test')->name('test');
    Route::get('users', 'users')->name('users');
});


Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
