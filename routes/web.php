<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\LanguageController;

// Language Switcher
Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');

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
    Route::get('/dashboard/live-clients', [DashboardController::class, 'liveClients'])->name('dashboard.live-clients');
    
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
        Route::get('products/image-quality', [\App\Http\Controllers\ProductController::class, 'imageQualityHub'])->name('products.image-quality');
        Route::post('products/bulk-upgrade-4k', [\App\Http\Controllers\ProductController::class, 'bulkUpgrade4k'])->name('products.bulk-upgrade-4k');
        Route::get('products/{product}/search-4k-images', [\App\Http\Controllers\ProductController::class, 'search4kImages'])->name('products.search-4k-images');
        Route::post('products/{product}/apply-4k-image', [\App\Http\Controllers\ProductController::class, 'apply4kImage'])->name('products.apply-4k-image');
        Route::post('products/upload-editor-image', [\App\Http\Controllers\ProductController::class, 'uploadEditorImage'])->name('products.upload-editor-image');
        Route::post('products/{product}/update-order', [\App\Http\Controllers\ProductController::class, 'updateOrder'])->name('products.update-order');
        Route::post('products/{product}/images/reorder', [\App\Http\Controllers\ProductController::class, 'reorderImages'])->name('products.images.reorder');
        Route::post('products/{product}/images/{image}/make-primary', [\App\Http\Controllers\ProductController::class, 'makePrimaryImage'])->name('products.images.make-primary');
        Route::post('products/{product}/images/{image}/padding', [\App\Http\Controllers\ProductController::class, 'updateSingleImagePadding'])->name('products.images.padding');
        Route::post('products/{product}/images/apply-padding-all', [\App\Http\Controllers\ProductController::class, 'applyPaddingToAllImages'])->name('products.images.apply-padding-all');
        Route::resource('products', \App\Http\Controllers\ProductController::class);

        // Brands Management
        Route::post('brands/auto-detect', [\App\Http\Controllers\BrandController::class, 'autoDetect'])->name('brands.auto-detect');
        Route::resource('brands', \App\Http\Controllers\BrandController::class);

        // Product Tags & Badges Management
        Route::resource('product-tags', \App\Http\Controllers\ProductTagController::class);
    });
    
    // Categories Management
    Route::middleware('permission:manage_categories')->group(function () {
        Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    });
    
    // Orders Management  
    Route::middleware('permission:manage_orders')->group(function () {
        Route::post('orders/{order}/quick-status', [\App\Http\Controllers\OrderController::class, 'quickUpdateStatus'])->name('orders.quick-status');
        Route::resource('orders', \App\Http\Controllers\OrderController::class);
    });
    
    // Invoices Management
    Route::middleware('permission:manage_invoices')->group(function () {
        Route::post('invoices/smart-match-text', [\App\Http\Controllers\InvoiceController::class, 'smartMatchText'])->name('invoices.smart-match-text');
        Route::resource('invoices', \App\Http\Controllers\InvoiceController::class);
        Route::get('invoices/{invoice}/download', [\App\Http\Controllers\InvoiceController::class, 'download'])->name('invoices.download');
        Route::get('invoices/{invoice}/print', [\App\Http\Controllers\InvoiceController::class, 'print'])->name('invoices.print');
        Route::get('invoices/{invoice}/guarantee', [\App\Http\Controllers\InvoiceController::class, 'guarantee'])->name('invoices.guarantee');
        Route::get('invoices/{invoice}/guarantee/download', [\App\Http\Controllers\InvoiceController::class, 'downloadGuarantee'])->name('invoices.guarantee.download');
        Route::get('invoices/{invoice}/guarantee/print', [\App\Http\Controllers\InvoiceController::class, 'printGuarantee'])->name('invoices.guarantee.print');
        Route::get('invoices/{invoice}/delivery-note', [\App\Http\Controllers\InvoiceController::class, 'deliveryNote'])->name('invoices.delivery-note');
        Route::get('invoices/{invoice}/delivery-note/download', [\App\Http\Controllers\InvoiceController::class, 'downloadDeliveryNote'])->name('invoices.delivery-note.download');
        Route::get('invoices/{invoice}/delivery-note/print', [\App\Http\Controllers\InvoiceController::class, 'printDeliveryNote'])->name('invoices.delivery-note.print');
        Route::post('invoices/{invoice}/email', [\App\Http\Controllers\InvoiceController::class, 'email'])->name('invoices.email');
        Route::post('invoices/{invoice}/quick-status', [\App\Http\Controllers\InvoiceController::class, 'quickUpdateStatus'])->name('invoices.quick-status');
        Route::post('orders/{order}/generate-invoice', [\App\Http\Controllers\InvoiceController::class, 'generateFromOrder'])->name('orders.generate-invoice');
        Route::post('orders/{order}/generate-quote', [\App\Http\Controllers\InvoiceController::class, 'generateFromOrder'])->name('orders.generate-quote');
        Route::post('invoices/{invoice}/payments', [\App\Http\Controllers\PaymentController::class, 'store'])->name('payments.store');
    });
    
    // Customers Management
    Route::middleware('permission:manage_customers')->group(function () {
        Route::post('customers/blacklist/store', [\App\Http\Controllers\CustomerController::class, 'storeBlacklist'])->name('customers.blacklist.store');
        Route::post('customers/{customer}/toggle-blacklist', [\App\Http\Controllers\CustomerController::class, 'toggleBlacklist'])->name('customers.toggle-blacklist');
        Route::post('orders/{order}/toggle-blacklist', [\App\Http\Controllers\CustomerController::class, 'toggleBlacklistFromOrder'])->name('orders.toggle-blacklist');
        Route::resource('customers', \App\Http\Controllers\CustomerController::class);
        Route::post('customers/{customer}/add-points', [\App\Http\Controllers\CustomerController::class, 'addPoints'])->name('customers.add-points');
        Route::post('customers/{customer}/redeem-points', [\App\Http\Controllers\CustomerController::class, 'redeemPoints'])->name('customers.redeem-points');
        
        // Debtors Dashboard
        Route::get('debtors', [\App\Http\Controllers\DebtorsController::class, 'index'])->name('debtors.index');
        Route::get('debtors/download', [\App\Http\Controllers\DebtorsController::class, 'downloadPdf'])->name('debtors.download');

        // Client Credits, Balances & Payment History with Proof Image
        Route::get('client-credits', [\App\Http\Controllers\CustomerCreditController::class, 'index'])->name('client-credits.index');
        Route::get('client-credits/transactions', [\App\Http\Controllers\CustomerCreditController::class, 'transactions'])->name('client-credits.transactions');
        Route::post('client-credits/transactions', [\App\Http\Controllers\CustomerCreditController::class, 'storeTransaction'])->name('client-credits.transactions.store');
        Route::delete('client-credits/transactions/{transaction}', [\App\Http\Controllers\CustomerCreditController::class, 'destroyTransaction'])->name('client-credits.transactions.destroy');
        Route::post('customers/{customer}/credit-limit', [\App\Http\Controllers\CustomerCreditController::class, 'updateCreditLimit'])->name('customers.update-credit-limit');
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

        // Stores, Warehouses & Multi-Store Stock Locations
        Route::get('/stores', [\App\Http\Controllers\StoreController::class, 'index'])->name('stores.index');
        Route::post('/stores', [\App\Http\Controllers\StoreController::class, 'store'])->name('stores.store');
        Route::put('/stores/{store}', [\App\Http\Controllers\StoreController::class, 'update'])->name('stores.update');
        Route::delete('/stores/{store}', [\App\Http\Controllers\StoreController::class, 'destroy'])->name('stores.destroy');
        Route::get('/stores/stock', [\App\Http\Controllers\StoreController::class, 'stockIndex'])->name('stores.stock');
        Route::post('/stores/stock/location', [\App\Http\Controllers\StoreController::class, 'updateStockLocation'])->name('stores.stock.location');
        Route::post('/stores/stock/transfer', [\App\Http\Controllers\StoreController::class, 'transfer'])->name('stores.stock.transfer');
    });
    
    // Coupons & Promotions
    Route::middleware('permission:manage_coupons')->group(function () {
        Route::resource('coupons', \App\Http\Controllers\CouponController::class);
        Route::post('/coupons/validate-code', [\App\Http\Controllers\CouponController::class, 'validateCode'])->name('coupons.validate');
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
    
    // Advanced Analytics & Views Intelligence
    Route::middleware('permission:view_analytics')->group(function () {
        Route::get('/analytics/views', [\App\Http\Controllers\ViewsAnalyticsController::class, 'index'])->name('analytics.views');
        Route::get('/analytics/views/live-visitors', [\App\Http\Controllers\ViewsAnalyticsController::class, 'liveVisitors'])->name('analytics.views.live-visitors');
        Route::get('/analytics/views/export', [\App\Http\Controllers\ViewsAnalyticsController::class, 'export'])->name('analytics.views.export');
        Route::get('/analytics/views/product/{product}', [\App\Http\Controllers\ViewsAnalyticsController::class, 'productDetail'])->name('analytics.views.product');
        Route::get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/analytics/export', [\App\Http\Controllers\AnalyticsController::class, 'export'])->name('analytics.export');
    });
    
    // Reports
    Route::middleware('permission:view_reports')->group(function () {
        Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-pdf', [\App\Http\Controllers\ReportController::class, 'exportPDF'])->name('reports.export.pdf');
        Route::get('/reports/export-csv', [\App\Http\Controllers\ReportController::class, 'exportCSV'])->name('reports.export.csv');

        // Profitability & Benefits (Strictly Admin Only)
        Route::get('/reports/profitability', [\App\Http\Controllers\ProfitabilityController::class, 'index'])->name('reports.profitability');
        Route::get('/reports/profitability/costs', [\App\Http\Controllers\ProfitabilityController::class, 'manageCosts'])->name('reports.profitability.costs');
        Route::get('/reports/profitability/export-csv', [\App\Http\Controllers\ProfitabilityController::class, 'exportCsv'])->name('reports.profitability.export');
        Route::post('/reports/profitability/products/{product}/cost', [\App\Http\Controllers\ProfitabilityController::class, 'updateCostPrice'])->name('reports.profitability.update-cost');
        Route::post('/reports/profitability/costs/batch', [\App\Http\Controllers\ProfitabilityController::class, 'updateBatchCosts'])->name('reports.profitability.update-costs-batch');

        // Shipments & Inbound Purchase Costs Management (Strictly Admin Only)
        Route::get('/shipments', [\App\Http\Controllers\ShipmentController::class, 'index'])->name('shipments.index');
        Route::get('/shipments/create', [\App\Http\Controllers\ShipmentController::class, 'create'])->name('shipments.create');
        Route::post('/shipments', [\App\Http\Controllers\ShipmentController::class, 'store'])->name('shipments.store');
        Route::get('/shipments/search-products', [\App\Http\Controllers\ShipmentController::class, 'searchProducts'])->name('shipments.search-products');
        Route::get('/shipments/{shipment}', [\App\Http\Controllers\ShipmentController::class, 'show'])->name('shipments.show');
        Route::delete('/shipments/{shipment}', [\App\Http\Controllers\ShipmentController::class, 'destroy'])->name('shipments.destroy');
        Route::get('/shipments/product-history/{product}', [\App\Http\Controllers\ShipmentController::class, 'productHistory'])->name('shipments.product-history');
    });
    
    // Settings
    Route::middleware('permission:manage_settings')->group(function () {
        Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');
        Route::post('/settings/logo', [\App\Http\Controllers\SettingsController::class, 'uploadLogo'])->name('settings.logo');
        Route::delete('/settings/logo', [\App\Http\Controllers\SettingsController::class, 'removeLogo'])->name('settings.logo.remove');
        Route::post('/settings/stamp', [\App\Http\Controllers\SettingsController::class, 'uploadStamp'])->name('settings.stamp');
        Route::delete('/settings/stamp', [\App\Http\Controllers\SettingsController::class, 'removeStamp'])->name('settings.stamp.remove');
        Route::post('/settings/reset', [\App\Http\Controllers\SettingsController::class, 'reset'])->name('settings.reset');
        Route::post('/settings/test-email', [\App\Http\Controllers\SettingsController::class, 'sendTestEmail'])->name('settings.test-email');
        Route::post('/settings/seo-boost', [\App\Http\Controllers\SettingsController::class, 'boostSeoAndAiIndex'])->name('settings.seo-boost');

        // Centralized Logos & Icons Management Studio
        Route::get('/logos-icons', [\App\Http\Controllers\SettingsController::class, 'logosAndIcons'])->name('settings.logos-icons');
        Route::post('/logos-icons/save', [\App\Http\Controllers\SettingsController::class, 'saveLogosAndIcons'])->name('settings.logos-icons.save');
        Route::post('/logos-icons/upload', [\App\Http\Controllers\SettingsController::class, 'uploadBrandAsset'])->name('settings.logos-icons.upload');
        Route::delete('/logos-icons/remove', [\App\Http\Controllers\SettingsController::class, 'removeBrandAsset'])->name('settings.logos-icons.remove');
        Route::post('/logos-icons/category/{category}', [\App\Http\Controllers\SettingsController::class, 'quickUpdateCategoryAsset'])->name('settings.logos-icons.category');
        Route::post('/logos-icons/brand/{brand}', [\App\Http\Controllers\SettingsController::class, 'quickUpdateBrandLogo'])->name('settings.logos-icons.brand');
        
        // Custom Code Manager
        Route::resource('custom-codes', \App\Http\Controllers\CustomCodeController::class);
    });

    // Hero Slider & Banners Management
    Route::post('banners/position/{position}', [\App\Http\Controllers\BannerController::class, 'savePosition'])->name('banners.save-position');
    Route::resource('banners', \App\Http\Controllers\BannerController::class);

    // Home Page Product Sections & Order Management
    Route::get('home-sections', [\App\Http\Controllers\HomeSectionController::class, 'index'])->name('home-sections.index');
    Route::post('home-sections', [\App\Http\Controllers\HomeSectionController::class, 'update'])->name('home-sections.update');
    Route::get('home-sections/search-products', [\App\Http\Controllers\HomeSectionController::class, 'searchProducts'])->name('home-sections.search');

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

Route::get('/dev-preview-invoices', function () {
    \Illuminate\Support\Facades\Auth::loginUsingId(27);
    return redirect()->route('invoices.index');
});

