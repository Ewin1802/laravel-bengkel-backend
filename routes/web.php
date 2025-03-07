<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\ProductInController;
use App\Http\Controllers\ProductNamesController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\OrderItemController;

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
    return view('pages.auth.login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('home', function () {
        return view('pages.dashboard');
    })->name('home');

    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('discounts', DiscountController::class);
        Route::resource('product_names', ProductNamesController::class);
        Route::resource('product_ins', ProductInController::class);

        Route::get('/products/check-stock', [ProductController::class, 'checkStock'])->name('products.checkStock');
        Route::resource('products', ProductController::class)->except(['show']); // Hindari error method show()

        Route::resource('order', OrderController::class);
        Route::get('/order-reports', [OrderController::class, 'index'])->name('order_reports.index');
        // //post update products
        // Route::post('products/update/{id}', [ProductController::class, 'update'])->name('products.newupdate');

        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');

        Route::get('/top-products', [OrderItemController::class, 'index'])->name('top.products');
        // Route::resource('inventories', InventoryController::class);
        // Route::resource('inventory_out', InventoryOutController::class);
        // // Route::post('/bahans/store', [BahanController::class, 'store'])->name('bahans.store');
        // Route::resource('bahans', BahanController::class);
        // Route::get('/inventory-reports', [InventoryReportController::class, 'index'])->name('inventory.reports');
        // Route::get('/laporan-keuangan', [FinancialReportController::class, 'index'])->name('laporan.keuangan');
        Route::get('/laporan-keuangan', [FinancialReportController::class, 'index'])->name('financial.report');


    });

});
