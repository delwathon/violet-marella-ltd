<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnireCraftStore\AnireStoreController;
use App\Http\Controllers\Lounge\LoungeRootController;
use App\Http\Controllers\Lounge\ProductsController;
use App\Http\Controllers\Lounge\CustomersController;
use App\Http\Controllers\Lounge\CategoryController;
use App\Http\Controllers\Lounge\SalesController;
use App\Http\Controllers\Lounge\InventoryController;
use App\Http\Controllers\PropRental\PropRentalController;
use App\Http\Controllers\PropRental\PropsController;
use App\Http\Controllers\PropRental\RentalCustomersController;
use App\Http\Controllers\PhotoStudio\PhotoStudioController;
use App\Http\Controllers\PhotoStudio\StudioCustomerController;
use App\Http\Controllers\PhotoStudio\StudioRateController;
use App\Http\Controllers\PhotoStudio\StudioSessionController;
use App\Http\Controllers\PhotoStudio\StudioReportController;
use App\Http\Controllers\PhotoStudio\StudioManagementController;
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
    
    // Anire Craft Store    
    Route::prefix('anire-craft-store')->name('anire-craft-store.')->group(function () {
        // Main page
        Route::get('/', [AnireStoreController::class, 'index'])->name('index');
    });
    
    // ==================== LOUNGE/POS ====================
    Route::prefix('lounge')->name('lounge.')->group(function () {
        // Main POS Interface
        Route::get('/', [LoungeRootController::class, 'index'])->name('index');
        
        // Product Search & Management (used by POS)
        Route::get('/products/search', [LoungeRootController::class, 'searchProducts'])->name('products.search');
        Route::get('/product/{id}', [LoungeRootController::class, 'getProduct'])->name('product');
        
        // Cart Management
        Route::post('/cart/add', [LoungeRootController::class, 'addToCart'])->name('cart.add');
        Route::get('/cart', [LoungeRootController::class, 'getCart'])->name('cart.get');
        Route::get('/cart/summary', [LoungeRootController::class, 'getCartSummary'])->name('cart.summary');
        Route::post('/cart/update', [LoungeRootController::class, 'updateCart'])->name('cart.update');
        Route::delete('/cart/remove/{productId}', [LoungeRootController::class, 'removeFromCart'])->name('cart.remove');
        Route::post('/cart/clear', [LoungeRootController::class, 'clearCart'])->name('cart.clear');
        
        // Sales/Checkout
        Route::post('/checkout', [LoungeRootController::class, 'processSale'])->name('checkout');
        Route::get('/sale/{id}', [LoungeRootController::class, 'getSale'])->name('sale');
        
        // Customer Management (Quick access from POS)
        Route::get('/search-customer', [LoungeRootController::class, 'searchCustomer'])->name('search-customer');
        Route::post('/create-customer', [LoungeRootController::class, 'createCustomer'])->name('create-customer');
        
        // Reports
        Route::get('/daily-report', [LoungeRootController::class, 'getDailyReport'])->name('daily-report');
        
        
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
            Route::get('/{id}/receipt', [SalesController::class, 'receipt'])->name('receipt');
            
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
    });

    // ==================== PROP RENTAL ====================
    Route::prefix('prop-rental')->name('prop-rental.')->group(function () {
        // Dashboard & Reports
        Route::get('/dashboard', [PropRentalController::class, 'dashboard'])->name('dashboard');
        Route::get('/reports', [PropRentalController::class, 'reports'])->name('reports');
        
        // Main page (All Props view)
        Route::get('/', [PropRentalController::class, 'index'])->name('index');
        
        // Props Management
        Route::prefix('props')->name('props.')->group(function () {
            Route::get('/create', [PropsController::class, 'create'])->name('create');
            Route::post('/', [PropsController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [PropsController::class, 'edit'])->name('edit');
            Route::put('/{id}', [PropsController::class, 'update'])->name('update');
            Route::get('/{id}/delete', [PropsController::class, 'showDelete'])->name('delete');
            Route::delete('/{id}', [PropsController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/maintenance', [PropsController::class, 'showMaintenance'])->name('maintenance');
            Route::post('/{id}/maintenance', [PropsController::class, 'markMaintenance'])->name('mark-maintenance');
            Route::post('/{id}/complete-maintenance', [PropsController::class, 'completeMaintenance'])->name('complete-maintenance');
        });
        
        // Rentals Management
        Route::prefix('rentals')->name('rentals.')->group(function () {
            Route::get('/create', [PropRentalController::class, 'create'])->name('create');
            Route::post('/', [PropRentalController::class, 'store'])->name('store');
            Route::get('/{id}', [PropRentalController::class, 'show'])->name('show');
            Route::get('/{id}/extend', [PropRentalController::class, 'editExtend'])->name('extend-form');
            Route::post('/{id}/extend', [PropRentalController::class, 'extend'])->name('extend');
            Route::get('/{id}/return', [PropRentalController::class, 'showReturn'])->name('return-form');
            Route::post('/{id}/return', [PropRentalController::class, 'returnProp'])->name('return');
            Route::get('/{id}/cancel', [PropRentalController::class, 'showCancel'])->name('cancel-form');
            Route::post('/{id}/cancel', [PropRentalController::class, 'cancel'])->name('cancel');
            Route::get('/export/csv', [PropRentalController::class, 'export'])->name('export');
        });
        
        // Customers Management
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/create', [RentalCustomersController::class, 'create'])->name('create');
            Route::post('/', [RentalCustomersController::class, 'store'])->name('store');
            Route::get('/{id}', [RentalCustomersController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [RentalCustomersController::class, 'edit'])->name('edit');
            Route::put('/{id}', [RentalCustomersController::class, 'update'])->name('update');
            Route::get('/{id}/deactivate', [RentalCustomersController::class, 'showDeactivate'])->name('deactivate-form');
            Route::post('/{id}/deactivate', [RentalCustomersController::class, 'deactivate'])->name('deactivate');
            Route::post('/{id}/activate', [RentalCustomersController::class, 'activate'])->name('activate');
        });
        
        // Calendar AJAX helper
        Route::get('/calendar/data', [PropRentalController::class, 'calendarData'])->name('calendar-data');
    });
    
    // Photo Studio Routes
    Route::prefix('photo-studio')->name('photo-studio.')->group(function () {
        // Dashboard
        Route::get('/', [PhotoStudioController::class, 'index'])->name('index');
        Route::get('/dashboard', [PhotoStudioController::class, 'index'])->name('dashboard');
        
        // Session Management
        Route::get('/sessions/active', [PhotoStudioController::class, 'activeSessions'])->name('sessions.active');
        Route::get('/sessions/history', [StudioSessionController::class, 'index'])->name('sessions.history');
        Route::post('/check-in', [PhotoStudioController::class, 'checkIn'])->name('check-in');
        Route::post('/checkout/{id}', [PhotoStudioController::class, 'checkout'])->name('checkout');
        Route::get('/session/{id}', [PhotoStudioController::class, 'getSession'])->name('session');
        Route::post('/extend/{id}', [PhotoStudioController::class, 'extendSession'])->name('extend');
        
        // QR Code
        Route::get('/session/{id}/qr-code', [PhotoStudioController::class, 'generateQRCode'])->name('generate-qr');
        Route::post('/scan-qr', [PhotoStudioController::class, 'scanQRCode'])->name('scan-qr');
        
        // AJAX Endpoints
        Route::get('/active-sessions', [PhotoStudioController::class, 'getActiveSessions'])->name('get-active-sessions');
        Route::get('/studio/{id}/status', [PhotoStudioController::class, 'getStudioStatus'])->name('studio-status');
        Route::get('/customers/search', [PhotoStudioController::class, 'searchCustomers'])->name('customers-search');
        
        // Customers Management
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [StudioCustomerController::class, 'index'])->name('index');
            Route::get('/create', [StudioCustomerController::class, 'create'])->name('create');
            Route::post('/', [StudioCustomerController::class, 'store'])->name('store');
            Route::get('/{id}', [StudioCustomerController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [StudioCustomerController::class, 'edit'])->name('edit');
            Route::put('/{id}', [StudioCustomerController::class, 'update'])->name('update');
            Route::delete('/{id}', [StudioCustomerController::class, 'destroy'])->name('destroy');
            Route::get('/export/csv', [StudioCustomerController::class, 'export'])->name('export');
        });
        
        // Sessions
        Route::prefix('sessions')->name('sessions.')->group(function () {
            Route::get('/', [StudioSessionController::class, 'index'])->name('index');
            Route::get('/{id}', [StudioSessionController::class, 'show'])->name('show');
            Route::get('/export/csv', [StudioSessionController::class, 'export'])->name('export');
        });
        
        // Studio Management
        Route::prefix('studios')->name('studios.')->group(function () {
            Route::get('/', [StudioManagementController::class, 'index'])->name('index');
            Route::get('/create', [StudioManagementController::class, 'create'])->name('create');
            Route::post('/', [StudioManagementController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [StudioManagementController::class, 'edit'])->name('edit');
            Route::put('/{id}', [StudioManagementController::class, 'update'])->name('update');
            Route::post('/{id}/status', [StudioManagementController::class, 'updateStatus'])->name('update-status');
            Route::delete('/{id}', [StudioManagementController::class, 'destroy'])->name('destroy');
        });
        
        // Rates Management
        Route::prefix('rates')->name('rates.')->group(function () {
            Route::get('/', [StudioRateController::class, 'index'])->name('index');
            Route::get('/list', [StudioRateController::class, 'getRatesList'])->name('list');
            Route::get('/create', [StudioRateController::class, 'create'])->name('create');
            Route::post('/', [StudioRateController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [StudioRateController::class, 'edit'])->name('edit');
            Route::put('/{id}', [StudioRateController::class, 'update'])->name('update');
            Route::post('/{id}/set-default', [StudioRateController::class, 'setDefault'])->name('set-default');
            Route::delete('/{id}', [StudioRateController::class, 'destroy'])->name('destroy');
        });
        
        // AJAX endpoint for rates list
        Route::get('/rates-list', [StudioRateController::class, 'getRatesList'])->name('rates-list');
        
        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [StudioReportController::class, 'index'])->name('index');
            Route::get('/daily', [StudioReportController::class, 'daily'])->name('daily');
            Route::get('/revenue', [StudioReportController::class, 'revenue'])->name('revenue');
            Route::get('/occupancy', [StudioReportController::class, 'occupancy'])->name('occupancy');
            Route::get('/customers', [StudioReportController::class, 'customers'])->name('customers');
            Route::get('/export', [StudioReportController::class, 'export'])->name('export');
        });
    });

    
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