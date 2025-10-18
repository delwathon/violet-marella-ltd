<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GiftStoreController;
use App\Http\Controllers\LoungeController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InstrumentRentalController;
use App\Http\Controllers\MusicStudioController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UsersController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login
Route::get('/', fn() => redirect()->route('login'));

// -------------------------------
// Authentication Routes
// -------------------------------
Route::prefix('auth')->group(function () {
    Route::get('login', [LoginController::class, 'index'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});

Auth::routes(['register' => false]);

// -------------------------------
// Application Routes (Protected)
// -------------------------------
Route::prefix('app')->middleware(['auth:user'])->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Gift Store
    Route::get('gift-store', [GiftStoreController::class, 'index'])->name('gift-store.index');
    
    // ==================== PRODUCT MANAGEMENT ====================
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductsController::class, 'index'])->name('index');
        Route::get('/create', [ProductsController::class, 'create'])->name('create');
        Route::post('/', [ProductsController::class, 'store'])->name('store');
        Route::get('/{id}', [ProductsController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ProductsController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ProductsController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProductsController::class, 'destroy'])->name('destroy');
        
        // AJAX endpoints
        Route::post('/{id}/adjust-stock', [ProductsController::class, 'adjustStock'])->name('adjust-stock');
        Route::get('/barcode/scan', [ProductsController::class, 'getByBarcode'])->name('barcode');
        Route::get('/low-stock/list', [ProductsController::class, 'getLowStock'])->name('low-stock');
        Route::get('/export/csv', [ProductsController::class, 'export'])->name('export');
    });
    
    // ==================== CUSTOMER MANAGEMENT ====================
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomersController::class, 'index'])->name('index');
        Route::get('/create', [CustomersController::class, 'create'])->name('create');
        Route::post('/', [CustomersController::class, 'store'])->name('store');
        Route::get('/{id}', [CustomersController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [CustomersController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CustomersController::class, 'update'])->name('update');
        Route::delete('/{id}', [CustomersController::class, 'destroy'])->name('destroy');
        
        // AJAX endpoints
        Route::get('/search/query', [CustomersController::class, 'search'])->name('search');
        Route::post('/quick/create', [CustomersController::class, 'quickStore'])->name('quick-store');
        Route::get('/{id}/details', [CustomersController::class, 'getCustomer'])->name('details');
        Route::post('/{id}/loyalty', [CustomersController::class, 'adjustLoyaltyPoints'])->name('adjust-loyalty');
        Route::get('/{id}/statistics', [CustomersController::class, 'getStatistics'])->name('statistics');
        Route::get('/export/csv', [CustomersController::class, 'export'])->name('export');
    });
    
    // ==================== CATEGORY MANAGEMENT ====================
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{id}', [CategoryController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
        
        // AJAX endpoints
        Route::get('/active/list', [CategoryController::class, 'getActive'])->name('active');
    });
    
    // ==================== SALES MANAGEMENT ====================
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [SalesController::class, 'index'])->name('index');
        Route::get('/today', [SalesController::class, 'today'])->name('today');
        Route::get('/create', [SalesController::class, 'create'])->name('create');
        Route::get('/{id}', [SalesController::class, 'show'])->name('show');
        Route::get('/{id}/receipt', [SalesController::class, 'receipt'])->name('receipt'); // ADD THIS LINE
        
        // AJAX endpoints
        Route::get('/export/csv', [SalesController::class, 'export'])->name('export');
        Route::get('/statistics/data', [SalesController::class, 'statistics'])->name('statistics');
    });
    
    // ==================== INVENTORY MANAGEMENT ====================
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/logs', [InventoryController::class, 'logs'])->name('logs');
        Route::get('/{id}/adjust', [InventoryController::class, 'adjust'])->name('adjust');
        Route::post('/{id}/adjust', [InventoryController::class, 'processAdjustment'])->name('process-adjustment');
        Route::get('/low-stock', [InventoryController::class, 'lowStock'])->name('low-stock');
        
        // AJAX endpoints
        Route::get('/export/csv', [InventoryController::class, 'export'])->name('export');
        Route::get('/statistics/data', [InventoryController::class, 'statistics'])->name('statistics');
    });
    
    // ==================== LOUNGE/POS ====================
    Route::prefix('lounge')->name('lounge.')->group(function () {
        // Main POS Interface
        Route::get('/', [LoungeController::class, 'index'])->name('index');
        
        // Product Search & Management (used by POS)
        Route::get('/products/search', [LoungeController::class, 'searchProducts'])->name('products.search');
        Route::get('/product/{id}', [LoungeController::class, 'getProduct'])->name('product');
        
        // Cart Management
        Route::post('/cart/add', [LoungeController::class, 'addToCart'])->name('cart.add');
        Route::get('/cart', [LoungeController::class, 'getCart'])->name('cart.get');
        Route::get('/cart/summary', [LoungeController::class, 'getCartSummary'])->name('cart.summary');
        Route::post('/cart/update', [LoungeController::class, 'updateCart'])->name('cart.update');
        Route::delete('/cart/remove/{productId}', [LoungeController::class, 'removeFromCart'])->name('cart.remove');
        Route::post('/cart/clear', [LoungeController::class, 'clearCart'])->name('cart.clear');
        
        // Sales/Checkout
        Route::post('/checkout', [LoungeController::class, 'processSale'])->name('checkout');
        Route::get('/sale/{id}', [LoungeController::class, 'getSale'])->name('sale');
        
        // Customer Management (Quick access from POS)
        Route::get('/search-customer', [LoungeController::class, 'searchCustomer'])->name('search-customer');
        Route::post('/create-customer', [LoungeController::class, 'createCustomer'])->name('create-customer');
        
        // Reports
        Route::get('/daily-report', [LoungeController::class, 'getDailyReport'])->name('daily-report');
    });
    
    // Instrument Rental
    Route::get('instrument-rental', [InstrumentRentalController::class, 'index'])->name('instrument-rental.index');
    
    // Music Studio
    Route::get('music-studio', [MusicStudioController::class, 'index'])->name('music-studio.index');
    
    // Reports
    Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');
    
    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');

    // Only accessible to users with 'admin' role
    Route::middleware(['auth:user,admin'])->group(function () {
        Route::get('users', [UsersController::class, 'index'])->name('users.index');
    });
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');