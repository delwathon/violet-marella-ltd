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
use App\Http\Controllers\AnireCraftStore\StoreRootController;
use App\Http\Controllers\AnireCraftStore\StoreProductsController;
use App\Http\Controllers\AnireCraftStore\StoreCustomersController;
use App\Http\Controllers\AnireCraftStore\StoreCategoryController;
use App\Http\Controllers\AnireCraftStore\StoreSalesController;
use App\Http\Controllers\AnireCraftStore\StoreInventoryController;
use App\Http\Controllers\PropRental\PropRentalController;
use App\Http\Controllers\PropRental\PropsController;
use App\Http\Controllers\PropRental\RentalCustomersController;
use App\Http\Controllers\PhotoStudio\PhotoStudioController;
use App\Http\Controllers\PhotoStudio\StudioSettingsController;
use App\Http\Controllers\PhotoStudio\StudioCategoryController;
use App\Http\Controllers\PhotoStudio\StudioRoomController;
use App\Http\Controllers\PhotoStudio\StudioCustomerController;
use App\Http\Controllers\PhotoStudio\StudioSessionController;
use App\Http\Controllers\PhotoStudio\StudioReportController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UsersController;
// New User Management Controllers
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\SecurityController;

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

// Auth::routes(['register' => false]);

// -------------------------------
// Application Routes (Protected)
// -------------------------------
Route::prefix('app')->middleware(['auth:user'])->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
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

            
            // Bulk Import
            Route::get('/bulk-upload', [ProductsController::class, 'bulkUploadPage'])->name('bulk-upload');
            Route::get('/download-template', [ProductsController::class, 'downloadTemplate'])->name('download-template');
            Route::post('/import-csv', [ProductsController::class, 'importCSV'])->name('import-csv');

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
    
    // ==================== ANIRE CRAFT STORE ====================
    Route::prefix('anire-craft-store')->name('anire-craft-store.')->group(function () {
        // Main POS Interface
        Route::get('/', [StoreRootController::class, 'index'])->name('index');
        
        // Product Search & Management (used by POS)
        Route::get('/products/search', [StoreRootController::class, 'searchProducts'])->name('products.search');
        Route::get('/product/{id}', [StoreRootController::class, 'getProduct'])->name('product');
        
        // Cart Management
        Route::post('/cart/add', [StoreRootController::class, 'addToCart'])->name('cart.add');
        Route::get('/cart', [StoreRootController::class, 'getCart'])->name('cart.get');
        Route::get('/cart/summary', [StoreRootController::class, 'getCartSummary'])->name('cart.summary');
        Route::post('/cart/update', [StoreRootController::class, 'updateCart'])->name('cart.update');
        Route::delete('/cart/remove/{productId}', [StoreRootController::class, 'removeFromCart'])->name('cart.remove');
        Route::post('/cart/clear', [StoreRootController::class, 'clearCart'])->name('cart.clear');
        
        // Sales/Checkout
        Route::post('/checkout', [StoreRootController::class, 'processSale'])->name('checkout');
        Route::get('/sale/{id}', [StoreRootController::class, 'getSale'])->name('sale');
        
        // Customer Management (Quick access from POS)
        Route::get('/search-customer', [StoreRootController::class, 'searchCustomer'])->name('search-customer');
        Route::post('/create-customer', [StoreRootController::class, 'createCustomer'])->name('create-customer');

        // Reports
        Route::get('/daily-report', [StoreRootController::class, 'getDailyReport'])->name('daily-report');
        
        // ==================== PRODUCT MANAGEMENT ====================
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [StoreProductsController::class, 'index'])->name('index');
            Route::get('/create', [StoreProductsController::class, 'create'])->name('create');
            Route::post('/', [StoreProductsController::class, 'store'])->name('store');
            
            // Bulk Import
            Route::get('/bulk-upload', [StoreProductsController::class, 'bulkUploadPage'])->name('bulk-upload');
            Route::get('/download-template', [StoreProductsController::class, 'downloadTemplate'])->name('download-template');
            Route::post('/import-csv', [StoreProductsController::class, 'importCSV'])->name('import-csv');
            
            Route::get('/{id}', [StoreProductsController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [StoreProductsController::class, 'edit'])->name('edit');
            Route::put('/{id}', [StoreProductsController::class, 'update'])->name('update');
            Route::delete('/{id}', [StoreProductsController::class, 'destroy'])->name('destroy');
            
            // AJAX endpoints
            Route::post('/{id}/adjust-stock', [StoreProductsController::class, 'adjustStock'])->name('adjust-stock');
            Route::get('/barcode/scan', [StoreProductsController::class, 'getByBarcode'])->name('barcode');
            Route::get('/low-stock/list', [StoreProductsController::class, 'getLowStock'])->name('low-stock');
            Route::get('/export/csv', [StoreProductsController::class, 'export'])->name('export');            
        });
        
        // ==================== CATEGORY MANAGEMENT ====================
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [StoreCategoryController::class, 'index'])->name('index');
            Route::get('/create', [StoreCategoryController::class, 'create'])->name('create');
            Route::post('/', [StoreCategoryController::class, 'store'])->name('store');
            Route::get('/{id}', [StoreCategoryController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [StoreCategoryController::class, 'edit'])->name('edit');
            Route::put('/{id}', [StoreCategoryController::class, 'update'])->name('update');
            Route::delete('/{id}', [StoreCategoryController::class, 'destroy'])->name('destroy');
            
            // AJAX endpoints
            Route::get('/active/list', [StoreCategoryController::class, 'getActive'])->name('active');
        });
        
        // ==================== CUSTOMER MANAGEMENT ====================
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [StoreCustomersController::class, 'index'])->name('index');
            Route::get('/create', [StoreCustomersController::class, 'create'])->name('create');
            Route::post('/', [StoreCustomersController::class, 'store'])->name('store');
            Route::get('/{id}', [StoreCustomersController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [StoreCustomersController::class, 'edit'])->name('edit');
            Route::put('/{id}', [StoreCustomersController::class, 'update'])->name('update');
            Route::delete('/{id}', [StoreCustomersController::class, 'destroy'])->name('destroy');
            
            // AJAX endpoints
            Route::get('/search/query', [StoreCustomersController::class, 'search'])->name('search');
            Route::post('/quick/create', [StoreCustomersController::class, 'quickStore'])->name('quick-store');
            Route::get('/{id}/details', [StoreCustomersController::class, 'getCustomer'])->name('details');
            Route::post('/{id}/loyalty', [StoreCustomersController::class, 'adjustLoyaltyPoints'])->name('adjust-loyalty');
            Route::get('/{id}/statistics', [StoreCustomersController::class, 'getStatistics'])->name('statistics');
            Route::get('/export/csv', [StoreCustomersController::class, 'export'])->name('export');
        });
        
        // ==================== SALES MANAGEMENT ====================
        Route::prefix('sales')->name('sales.')->group(function () {
            Route::get('/', [StoreSalesController::class, 'index'])->name('index');
            Route::get('/today', [StoreSalesController::class, 'today'])->name('today');
            Route::get('/create', [StoreSalesController::class, 'create'])->name('create');
            Route::get('/{id}', [StoreSalesController::class, 'show'])->name('show');
            Route::get('/{id}/receipt', [StoreSalesController::class, 'receipt'])->name('receipt');
            
            // AJAX endpoints
            Route::get('/export/csv', [StoreSalesController::class, 'export'])->name('export');
            Route::get('/statistics/data', [StoreSalesController::class, 'statistics'])->name('statistics');
        });
        
        // ==================== INVENTORY MANAGEMENT ====================
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/', [StoreInventoryController::class, 'index'])->name('index');
            Route::get('/logs', [StoreInventoryController::class, 'logs'])->name('logs');
            Route::get('/{id}/adjust', [StoreInventoryController::class, 'adjust'])->name('adjust');
            Route::post('/{id}/adjust', [StoreInventoryController::class, 'processAdjustment'])->name('process-adjustment');
            Route::get('/low-stock', [StoreInventoryController::class, 'lowStock'])->name('low-stock');
            
            // AJAX endpoints
            Route::get('/export/csv', [StoreInventoryController::class, 'export'])->name('export');
            Route::get('/statistics/data', [StoreInventoryController::class, 'statistics'])->name('statistics');
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
    
    Route::prefix('photo-studio')->name('photo-studio.')->group(function () {
        // Dashboard & Main Operations
        Route::get('/', [PhotoStudioController::class, 'index'])->name('index');
        Route::get('/dashboard', [PhotoStudioController::class, 'index'])->name('dashboard');
        
        // Active Sessions
        Route::get('/sessions/active', [PhotoStudioController::class, 'activeSessions'])->name('sessions.active');
        Route::get('/active-sessions', [PhotoStudioController::class, 'getActiveSessions'])->name('get-active-sessions');
        
        // Check-in & Checkout
        Route::post('/check-in', [PhotoStudioController::class, 'checkIn'])->name('check-in');
        Route::post('/checkout/{id}', [PhotoStudioController::class, 'checkout'])->name('checkout');
        
        // Session Operations
        Route::post('/start-timer/{id}', [PhotoStudioController::class, 'startTimer'])->name('start-timer');
        Route::get('/session/{id}', [PhotoStudioController::class, 'getSession'])->name('session');
        Route::post('/extend/{id}', [PhotoStudioController::class, 'extendSession'])->name('extend');
        Route::post('/cancel/{id}', [PhotoStudioController::class, 'cancelSession'])->name('cancel');
        
        // QR Code Operations
        Route::get('/session/{id}/qr-code', [PhotoStudioController::class, 'generateQRCode'])->name('generate-qr');
        Route::post('/scan-qr', [PhotoStudioController::class, 'scanQRCode'])->name('scan-qr');
        
        // Customer Search
        Route::get('/customers/search', [PhotoStudioController::class, 'searchCustomers'])->name('customers-search');
        
        // ==================== SETTINGS ====================
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [StudioSettingsController::class, 'index'])->name('index');
            Route::post('/offset-time', [StudioSettingsController::class, 'updateOffsetTime'])->name('offset-time');
            Route::post('/base-time', [StudioSettingsController::class, 'updateDefaultBaseTime'])->name('base-time');
            Route::post('/base-price', [StudioSettingsController::class, 'updateDefaultBasePrice'])->name('base-price');
            Route::post('/overtime', [StudioSettingsController::class, 'updateAllowOvertime'])->name('overtime');
            Route::post('/update', [StudioSettingsController::class, 'updateSetting'])->name('update');
            Route::get('/get/{key}', [StudioSettingsController::class, 'getSetting'])->name('get');
            Route::get('/all', [StudioSettingsController::class, 'getAllSettings'])->name('all');
            Route::post('/reset', [StudioSettingsController::class, 'resetToDefaults'])->name('reset');
            Route::post('/clear-cache', [StudioSettingsController::class, 'clearCache'])->name('clear-cache');
        });
        
        // ==================== CATEGORIES ====================
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [StudioCategoryController::class, 'index'])->name('index');
            Route::get('/create', [StudioCategoryController::class, 'create'])->name('create');
            Route::post('/', [StudioCategoryController::class, 'store'])->name('store');
            Route::get('/{id}', [StudioCategoryController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [StudioCategoryController::class, 'edit'])->name('edit');
            Route::put('/{id}', [StudioCategoryController::class, 'update'])->name('update');
            Route::delete('/{id}', [StudioCategoryController::class, 'destroy'])->name('destroy');
            
            // AJAX endpoints
            Route::get('/available/list', [StudioCategoryController::class, 'getAvailableCategories'])->name('available');
            Route::get('/{id}/details', [StudioCategoryController::class, 'getDetails'])->name('details');
            Route::post('/{id}/calculate-price', [StudioCategoryController::class, 'calculatePrice'])->name('calculate-price');
            Route::get('/{id}/statistics', [StudioCategoryController::class, 'getStatistics'])->name('statistics');
            Route::post('/{id}/toggle-active', [StudioCategoryController::class, 'toggleActive'])->name('toggle-active');
            Route::post('/sort-order', [StudioCategoryController::class, 'updateSortOrder'])->name('sort-order');
            Route::post('/{id}/duplicate', [StudioCategoryController::class, 'duplicate'])->name('duplicate');
        });
        
        // ==================== ROOMS (OPTIONAL) ====================
        Route::prefix('rooms')->name('rooms.')->group(function () {
            Route::get('/', [StudioRoomController::class, 'index'])->name('index');
            Route::get('/create', [StudioRoomController::class, 'create'])->name('create');
            Route::post('/', [StudioRoomController::class, 'store'])->name('store');
            Route::get('/{id}', [StudioRoomController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [StudioRoomController::class, 'edit'])->name('edit');
            Route::put('/{id}', [StudioRoomController::class, 'update'])->name('update');
            Route::delete('/{id}', [StudioRoomController::class, 'destroy'])->name('destroy');
            
            // Status Management
            Route::post('/{id}/status', [StudioRoomController::class, 'updateStatus'])->name('update-status');
            Route::post('/{id}/maintenance', [StudioRoomController::class, 'markMaintenance'])->name('maintenance');
            Route::post('/{id}/available', [StudioRoomController::class, 'markAvailable'])->name('mark-available');
            Route::post('/{id}/out-of-service', [StudioRoomController::class, 'markOutOfService'])->name('out-of-service');
            
            // Equipment Management
            Route::post('/{id}/equipment/add', [StudioRoomController::class, 'addEquipment'])->name('equipment.add');
            Route::post('/{id}/equipment/remove', [StudioRoomController::class, 'removeEquipment'])->name('equipment.remove');
            
            // AJAX endpoints
            Route::get('/category/{categoryId}', [StudioRoomController::class, 'getByCategory'])->name('by-category');
            Route::get('/category/{categoryId}/available', [StudioRoomController::class, 'getAvailableByCategory'])->name('available-by-category');
            Route::get('/export', [StudioRoomController::class, 'export'])->name('export');
        });
        
        // ==================== CUSTOMERS ====================
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [StudioCustomerController::class, 'index'])->name('index');
            Route::get('/create', [StudioCustomerController::class, 'create'])->name('create');
            Route::post('/', [StudioCustomerController::class, 'store'])->name('store');
            Route::get('/{id}', [StudioCustomerController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [StudioCustomerController::class, 'edit'])->name('edit');
            Route::put('/{id}', [StudioCustomerController::class, 'update'])->name('update');
            Route::delete('/{id}', [StudioCustomerController::class, 'destroy'])->name('destroy');
            
            // Customer Management
            Route::post('/{id}/blacklist', [StudioCustomerController::class, 'blacklist'])->name('blacklist');
            Route::post('/{id}/remove-blacklist', [StudioCustomerController::class, 'removeFromBlacklist'])->name('remove-blacklist');
            Route::post('/{id}/update-statistics', [StudioCustomerController::class, 'updateStatistics'])->name('update-statistics');
            
            Route::get('/export/csv', [StudioCustomerController::class, 'export'])->name('export');
        });
        
        // ==================== SESSIONS ====================
        Route::prefix('sessions')->name('sessions.')->group(function () {
            Route::get('/', [StudioSessionController::class, 'index'])->name('index');
            Route::get('/{id}', [StudioSessionController::class, 'show'])->name('show');
            Route::post('/{id}/payment', [StudioSessionController::class, 'processPayment'])->name('payment');
            Route::get('/export/csv', [StudioSessionController::class, 'export'])->name('export');
        });
        
        // ==================== REPORTS ====================
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [StudioReportController::class, 'index'])->name('index');
            Route::get('/daily', [StudioReportController::class, 'daily'])->name('daily');
            Route::get('/revenue', [StudioReportController::class, 'revenue'])->name('revenue');
            Route::get('/occupancy', [StudioReportController::class, 'occupancy'])->name('occupancy');
            Route::get('/customers', [StudioReportController::class, 'customers'])->name('customers');
            Route::get('/category-performance', [StudioReportController::class, 'categoryPerformance'])->name('category-performance');
            Route::get('/export', [StudioReportController::class, 'export'])->name('export');
        });
    });

    
    // Reports
    Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');
    
    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');

    // =============================================
    // USER MANAGEMENT ROUTES
    // =============================================
    
    // Place specific routes BEFORE dynamic {id} routes to avoid conflicts
    Route::get('/users/activity/log', [UserController::class, 'activity'])->name('users.activity');
    Route::get('/users/security/settings', [UserController::class, 'security'])->name('users.security');
    Route::get('/users/download-template', [UserController::class, 'downloadTemplate'])->name('users.download-template');
    Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    
    // Main user CRUD operations (general routes after specific routes)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    
    // User permissions management
    Route::get('/users/{id}/permissions', [UserController::class, 'permissions'])->name('users.permissions');
    Route::post('/users/{id}/permissions', [UserController::class, 'updatePermissions'])->name('users.permissions.update');
    
    // Import & Bulk operations
    Route::post('/users/import', [UserController::class, 'import'])->name('users.import');
    Route::post('/users/bulk-activate', [UserController::class, 'bulkActivate'])->name('users.bulk-activate');
    Route::post('/users/bulk-suspend', [UserController::class, 'bulkSuspend'])->name('users.bulk-suspend');
    Route::post('/users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');
    Route::post('/users/bulk-assign-role', [UserController::class, 'bulkAssignRole'])->name('users.bulk-assign-role');
    
    // =============================================
    // ROLE MANAGEMENT ROUTES
    // =============================================
    
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{id}', [RoleController::class, 'show'])->name('roles.show');
    Route::put('/roles/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::post('/roles/{id}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
    Route::post('/roles/{id}/duplicate', [RoleController::class, 'duplicate'])->name('roles.duplicate');
    
    // =============================================
    // DEPARTMENT MANAGEMENT ROUTES
    // =============================================
    
    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::get('/departments/{id}', [DepartmentController::class, 'show'])->name('departments.show');
    Route::put('/departments/{id}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('/departments/{id}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
    Route::get('/departments/{id}/members', [DepartmentController::class, 'members'])->name('departments.members');
    Route::post('/departments/{id}/members', [DepartmentController::class, 'addMember'])->name('departments.add-member');
    Route::delete('/departments/{id}/members/{userId}', [DepartmentController::class, 'removeMember'])->name('departments.remove-member');
    
    // =============================================
    // SECURITY SETTINGS ROUTES
    // =============================================
    
    Route::post('/security/password-policy', [SecurityController::class, 'updatePasswordPolicy'])->name('users.security.password-policy');
    Route::post('/security/authentication', [SecurityController::class, 'updateAuthentication'])->name('users.security.authentication');
    Route::post('/security/audit-log', [SecurityController::class, 'updateAuditLog'])->name('users.security.audit-log');
    Route::post('/security/ip-whitelist', [SecurityController::class, 'addIpWhitelist'])->name('security.ip-whitelist.add');
    Route::delete('/security/ip-whitelist/{index}', [SecurityController::class, 'removeIpWhitelist'])->name('security.ip-whitelist.remove');
    Route::post('/security/ip-blacklist', [SecurityController::class, 'addIpBlacklist'])->name('security.ip-blacklist.add');
    Route::delete('/security/ip-blacklist/{index}', [SecurityController::class, 'removeIpBlacklist'])->name('security.ip-blacklist.remove');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');