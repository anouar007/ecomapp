<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes  
|--------------------------------------------------------------------------
|
| Core application routes for authentication and protected dashboards.
| Shop routes are in routes/shop.php
| Admin routes are in routes/admin.php
|
*/

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:5,1');
    
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])
        ->middleware('throttle:3,1');
});

// Protected routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Customer Dashboard
    Route::prefix('my-account')->name('customer.')->group(function () {
        Route::get('/', [CustomerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/orders', [CustomerDashboardController::class, 'orders'])->name('orders');
        Route::get('/orders/{order}', [CustomerDashboardController::class, 'orderShow'])->name('orders.show');
        Route::get('/profile', [CustomerDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [CustomerDashboardController::class, 'updateProfile'])->name('profile.update');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Management
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    
    // POS Terminal
    Route::middleware('permission:manage_orders')->group(function () {
        Route::get('/pos', [\App\Http\Controllers\POSController::class, 'index'])->name('pos.index');
        Route::get('/pos/search', [\App\Http\Controllers\POSController::class, 'search'])->name('pos.search');
        Route::post('/pos/order', [\App\Http\Controllers\POSController::class, 'processOrder'])->name('pos.order');
    });
    
    // Products Management
    Route::middleware('permission:manage_products')->group(function () {
        Route::post('products/import', [\App\Http\Controllers\ProductController::class, 'import'])->name('products.import');
        Route::get('products/template', [\App\Http\Controllers\ProductController::class, 'downloadTemplate'])->name('products.template');
        Route::post('products/bulk-action', [\App\Http\Controllers\ProductController::class, 'bulkAction'])->name('products.bulk-action');
        Route::resource('products', \App\Http\Controllers\ProductController::class);
    });
    
    // Categories Management
    Route::middleware('permission:manage_categories')->group(function () {
        Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    });
    
    // Orders Management  
    Route::middleware('permission:manage_orders')->group(function () {
        Route::resource('orders', \App\Http\Controllers\OrderController::class);
    });
    
    // Invoices Management
    Route::middleware('permission:manage_invoices')->group(function () {
        Route::resource('invoices', \App\Http\Controllers\InvoiceController::class);
        Route::get('invoices/{invoice}/download', [\App\Http\Controllers\InvoiceController::class, 'download'])->name('invoices.download');
        Route::get('invoices/{invoice}/print', [\App\Http\Controllers\InvoiceController::class, 'print'])->name('invoices.print');
        Route::post('invoices/{invoice}/email', [\App\Http\Controllers\InvoiceController::class, 'email'])->name('invoices.email');
        Route::post('orders/{order}/generate-invoice', [\App\Http\Controllers\InvoiceController::class, 'generateFromOrder'])->name('orders.generate-invoice');
        Route::post('invoices/{invoice}/payments', [\App\Http\Controllers\PaymentController::class, 'store'])->name('payments.store');
    });
    
    // Customers Management
    Route::middleware('permission:manage_customers')->group(function () {
        Route::resource('customers', \App\Http\Controllers\CustomerController::class);
        Route::post('customers/{customer}/add-points', [\App\Http\Controllers\CustomerController::class, 'addPoints'])->name('customers.add-points');
        Route::post('customers/{customer}/redeem-points', [\App\Http\Controllers\CustomerController::class, 'redeemPoints'])->name('customers.redeem-points');
        
        // Debtors Dashboard
        Route::get('debtors', [\App\Http\Controllers\DebtorsController::class, 'index'])->name('debtors.index');
        Route::get('debtors/download', [\App\Http\Controllers\DebtorsController::class, 'downloadPdf'])->name('debtors.download');
    });
    
    // Inventory Management
    Route::middleware('permission:manage_inventory')->group(function () {
        Route::get('/inventory/export', [\App\Http\Controllers\InventoryController::class, 'export'])->name('inventory.export');
        Route::post('/inventory/bulk-action', [\App\Http\Controllers\InventoryController::class, 'bulkAction'])->name('inventory.bulk-action');
        Route::get('/inventory', [\App\Http\Controllers\InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/movements', [\App\Http\Controllers\InventoryController::class, 'movements'])->name('inventory.movements');
        Route::get('/inventory/alerts', [\App\Http\Controllers\InventoryController::class, 'alerts'])->name('inventory.alerts');
        Route::get('/inventory/{product}/adjust', [\App\Http\Controllers\InventoryController::class, 'adjust'])->name('inventory.adjust');
        Route::post('/inventory/{product}/adjust', [\App\Http\Controllers\InventoryController::class, 'processAdjustment'])->name('inventory.process-adjustment');
        Route::post('/inventory/alerts/{alert}/acknowledge', [\App\Http\Controllers\InventoryController::class, 'acknowledgeAlert'])->name('inventory.acknowledge-alert');
        Route::post('/inventory/alerts/bulk-acknowledge', [\App\Http\Controllers\InventoryController::class, 'bulkAcknowledge'])->name('inventory.bulk-acknowledge');
    });
    
    // Coupons & Promotions
    Route::middleware('permission:manage_coupons')->group(function () {
        Route::resource('coupons', \App\Http\Controllers\CouponController::class);
        Route::post('/coupons/validate-code', [\App\Http\Controllers\CouponController::class, 'validate'])->name('coupons.validate');
        Route::get('/coupons/generate-code/random', [\App\Http\Controllers\CouponController::class, 'generate'])->name('coupons.generate');
    });
    
    // Activity Logs
    Route::middleware('permission:view_activity_logs')->group(function () {
        Route::get('/activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/{activityLog}', [\App\Http\Controllers\ActivityLogController::class, 'show'])->name('activity-logs.show');
        Route::post('/activity-logs/clear', [\App\Http\Controllers\ActivityLogController::class, 'clear'])->name('activity-logs.clear');
    });
    
    // Data Export
    Route::middleware('permission:export_data')->group(function () {
        Route::get('/export/products', [\App\Http\Controllers\ExportController::class, 'products'])->name('export.products');
        Route::get('/export/customers', [\App\Http\Controllers\ExportController::class, 'customers'])->name('export.customers');
        Route::get('/export/orders', [\App\Http\Controllers\ExportController::class, 'orders'])->name('export.orders');
        Route::get('/export/invoices', [\App\Http\Controllers\ExportController::class, 'invoices'])->name('export.invoices');
    });
    
    // Wishlist (requires authentication)
    Route::get('/wishlist', [\App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [\App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle');

    Route::middleware('permission:manage_reviews')->group(function () {
        Route::get('/reviews', [\App\Http\Controllers\ProductReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/{review}/approve', [\App\Http\Controllers\ProductReviewController::class, 'approve'])->name('reviews.approve');
        Route::post('/reviews/{review}/reject', [\App\Http\Controllers\ProductReviewController::class, 'reject'])->name('reviews.reject');
        Route::delete('/reviews/{review}', [\App\Http\Controllers\ProductReviewController::class, 'destroy'])->name('reviews.destroy');
    });
    
    // Returns & Refunds
    Route::middleware('permission:manage_returns')->group(function () {
        Route::get('/returns', [\App\Http\Controllers\ReturnController::class, 'index'])->name('returns.index');
        Route::get('/returns/{return}', [\App\Http\Controllers\ReturnController::class, 'show'])->name('returns.show');
        Route::post('/returns/{return}/approve', [\App\Http\Controllers\ReturnController::class, 'approve'])->name('returns.approve');
        Route::post('/returns/{return}/reject', [\App\Http\Controllers\ReturnController::class, 'reject'])->name('returns.reject');
        Route::post('/returns/{return}/complete', [\App\Http\Controllers\ReturnController::class, 'complete'])->name('returns.complete');
    });
    
    // Advanced Analytics
    Route::middleware('permission:view_analytics')->group(function () {
        Route::get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/analytics/export', [\App\Http\Controllers\AnalyticsController::class, 'export'])->name('analytics.export');
    });
    
    // Reports
    Route::middleware('permission:view_reports')->group(function () {
        Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-pdf', [\App\Http\Controllers\ReportController::class, 'exportPDF'])->name('reports.export.pdf');
        Route::get('/reports/export-csv', [\App\Http\Controllers\ReportController::class, 'exportCSV'])->name('reports.export.csv');
    });
    
    // Settings
    Route::middleware('permission:manage_settings')->group(function () {
        Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');
        Route::post('/settings/logo', [\App\Http\Controllers\SettingsController::class, 'uploadLogo'])->name('settings.logo');
        Route::delete('/settings/logo', [\App\Http\Controllers\SettingsController::class, 'removeLogo'])->name('settings.logo.remove');
        Route::post('/settings/reset', [\App\Http\Controllers\SettingsController::class, 'reset'])->name('settings.reset');
        
        // Custom Code Manager
        Route::resource('custom-codes', \App\Http\Controllers\CustomCodeController::class);
    });

    // Page & Navigation Management
    Route::middleware('permission:manage_content')->group(function () {
        Route::resource('pages', \App\Http\Controllers\PageController::class);
        Route::get('menus', [\App\Http\Controllers\MenuController::class, 'index'])->name('menus.index');
        Route::post('menus', [\App\Http\Controllers\MenuController::class, 'store'])->name('menus.store');
        Route::put('menus/{menu}/items', [\App\Http\Controllers\MenuController::class, 'updateItem'])->name('menus.items.update');
        Route::delete('menus/{menu}', [\App\Http\Controllers\MenuController::class, 'destroy'])->name('menus.destroy');

        // Media Manager Routes
        Route::get('media/list', [\App\Http\Controllers\MediaController::class, 'index'])->name('media.index');
        Route::post('media/upload', [\App\Http\Controllers\MediaController::class, 'store'])->name('media.store');
    }); 

    // Accounting Module
    Route::middleware('permission:manage_accounting')->prefix('accounting')->name('accounting.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AccountingController::class, 'index'])->name('index');
        Route::get('/accounts', [\App\Http\Controllers\AccountingController::class, 'accounts'])->name('accounts');
        Route::post('/accounts', [\App\Http\Controllers\AccountingController::class, 'storeAccount'])->name('accounts.store');
        Route::get('/entries', [\App\Http\Controllers\AccountingController::class, 'entries'])->name('entries');
        Route::get('/entries/create', [\App\Http\Controllers\AccountingController::class, 'createEntry'])->name('entries.create');
        Route::post('/entries', [\App\Http\Controllers\AccountingController::class, 'storeEntry'])->name('entries.store');
        Route::get('/reports', [\App\Http\Controllers\AccountingController::class, 'reports'])->name('reports');
        Route::get('/reports/general-ledger', [\App\Http\Controllers\AccountingController::class, 'generalLedger'])->name('reports.gl');
        Route::get('/reports/general-ledger/excel', [\App\Http\Controllers\AccountingController::class, 'exportGeneralLedgerExcel'])->name('reports.gl.excel');
        Route::get('/reports/bilan', [\App\Http\Controllers\AccountingController::class, 'balanceSheet'])->name('reports.bilan');
        Route::get('/reports/bilan/pdf', [\App\Http\Controllers\AccountingController::class, 'downloadBilanPDF'])->name('reports.bilan.pdf');
        Route::get('/reports/cpc', [\App\Http\Controllers\AccountingController::class, 'incomeStatement'])->name('reports.cpc');
        Route::get('/reports/cpc/pdf', [\App\Http\Controllers\AccountingController::class, 'downloadCpcPDF'])->name('reports.cpc.pdf');
    });
});

// Public routes - Reviews (accessible by guests)
Route::post('/reviews', [\App\Http\Controllers\ProductReviewController::class, 'store'])->name('reviews.store');
