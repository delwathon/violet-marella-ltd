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
    Route::get('test', 'test')->name('test');
    Route::get('users', 'users')->name('users');
});


// Staff Authentication Routes
Route::prefix('staff')->group(function () {
    Route::get('login', [App\Http\Controllers\Auth\StaffAuthController::class, 'showLoginForm'])->name('staff.login');
    Route::post('login', [App\Http\Controllers\Auth\StaffAuthController::class, 'login']);
    Route::post('logout', [App\Http\Controllers\Auth\StaffAuthController::class, 'logout'])->name('staff.logout');
});
Auth::routes();

// Supermarket POS System Routes
Route::prefix('supermarket')->middleware(['staff.auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\SupermarketController::class, 'index'])->name('supermarket.index');
    Route::get('dashboard', [App\Http\Controllers\Auth\StaffAuthController::class, 'dashboard'])->name('supermarket.dashboard');

    // POS Routes
    Route::get('pos', [App\Http\Controllers\SupermarketController::class, 'pos'])->name('supermarket.pos');
    Route::post('cart/add', [App\Http\Controllers\SupermarketController::class, 'addToCart'])->name('supermarket.cart.add');
    Route::post('cart/update', [App\Http\Controllers\SupermarketController::class, 'updateCart'])->name('supermarket.cart.update');
    Route::delete('cart/remove/{item}', [App\Http\Controllers\SupermarketController::class, 'removeFromCart'])->name('supermarket.cart.remove');
    Route::post('checkout', [App\Http\Controllers\SupermarketController::class, 'checkout'])->name('supermarket.checkout');

    // Cart Routes
    Route::get('cart', [App\Http\Controllers\SupermarketController::class, 'getCart'])->name('supermarket.cart.get');
    Route::get('cart/summary', [App\Http\Controllers\SupermarketController::class, 'getCartSummary'])->name('supermarket.cart.summary');
    Route::post('cart/clear', [App\Http\Controllers\SupermarketController::class, 'clearCart'])->name('supermarket.cart.clear');

    // Product Management
    Route::get('products', [App\Http\Controllers\SupermarketController::class, 'products'])->name('supermarket.products');
    Route::get('products/search', [App\Http\Controllers\SupermarketController::class, 'searchProducts'])->name('supermarket.products.search');

    // Customer Management
    Route::get('customers', [App\Http\Controllers\SupermarketController::class, 'customers'])->name('supermarket.customers');
    Route::post('customers', [App\Http\Controllers\SupermarketController::class, 'storeCustomer'])->name('supermarket.customers.store');

    // Sales & Reports
    Route::get('sales', [App\Http\Controllers\SupermarketController::class, 'sales'])->name('supermarket.sales');
    Route::get('sales/{sale}', [App\Http\Controllers\SupermarketController::class, 'showSale'])->name('supermarket.sales.show');
    Route::get('reports', [App\Http\Controllers\SupermarketController::class, 'reports'])->name('supermarket.reports');

    // Inventory Management
    Route::get('inventory', [App\Http\Controllers\SupermarketController::class, 'inventory'])->name('supermarket.inventory');
    Route::post('inventory/adjust', [App\Http\Controllers\SupermarketController::class, 'adjustInventory'])->name('supermarket.inventory.adjust');
});


Route::get('/home', [HomeController::class, 'index'])->name('home');
