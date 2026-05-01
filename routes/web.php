<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController as user;
use App\Http\Controllers\WorkingHourController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SppgController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\UomController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemVendorController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;
use Itstructure\LaRbac\Http\Controllers\{UserController, RoleController, PermissionController};

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
    return view('welcome');
});

Route::get('/dashboard',[DashboardController::class,'index_hr'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard_employee',[DashboardController::class,'index'])->middleware(['auth', 'verified'])->name('dashboard_employee');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard/get_daily', [DashboardController::class, 'get_daily'])->name('dashboard.get_daily');
    Route::get('/dashboard/get_weekly', [DashboardController::class, 'get_weekly'])->name('dashboard.get_weekly');
    Route::get('/dashboard/get_monthly', [DashboardController::class, 'get_monthly'])->name('dashboard.get_monthly');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('user/get_table', [user::class, 'get_table'])->name('user.get_table');

    Route::get('working/get_table', [WorkingHourController::class, 'get_table'])->name('working.get_table');

    Route::get('attendance/report', [PresenceController::class,'report'])->name('attendance_report');
    Route::get('attendance/log', [PresenceController::class,'log'])->name('attendance_log');

    Route::resource('user', user::class)->name('index','user');
    Route::resource('working', WorkingHourController::class)->name('index','working');
    Route::resource('attendance', PresenceController::class)->name('index','attendance');

    Route::get('sppg/get_table', [SppgController::class, 'get_table'])->name('sppg.get_table');
    Route::resource('sppg', SppgController::class)->name('index','sppg');

    Route::get('kategori/get_table', [KategoriController::class, 'get_table'])->name('kategori.get_table');
    Route::resource('kategori', KategoriController::class)->name('index','kategori');

    Route::get('uom/get_table', [UomController::class, 'get_table'])->name('uom.get_table');
    Route::resource('uom', UomController::class)->name('index','uom');

    Route::get('vendor/get_table', [VendorController::class, 'get_table'])->name('vendor.get_table');
    Route::post('vendor/import', [VendorController::class, 'import'])->name('vendor.import');
    Route::resource('vendor', VendorController::class)->name('index','vendor');

    Route::get('item/get_table', [ItemController::class, 'get_table'])->name('item.get_table');
    Route::post('item/import', [ItemController::class, 'import'])->name('item.import');
    Route::get('item-vendor/get_table', [ItemVendorController::class, 'get_table'])->name('item_vendor.get_table');
    Route::get('menu-item/get_table', [MenuController::class, 'get_table'])->name('item_menu.get_table');
    Route::get('purchase-order/get_table', [PurchaseOrderController::class, 'get_table'])->name('purchase_order.get_table');
    Route::get('menu-item/by-date', [MenuController::class, 'getByDate'])->name('item_menu.by_date');
    Route::post('menu-item/copy', [MenuController::class, 'copyFromDate'])->name('item_menu.copy');
    Route::get('purchase-order/generate', [PurchaseOrderController::class, 'generate'])->name('purchase_order.generate');
    Route::post('purchase-order/store', [PurchaseOrderController::class, 'store'])->name('purchase_order.store');
    Route::get('purchase-order/{purchaseOrder}/edit', [PurchaseOrderController::class, 'edit'])->name('purchase_order.edit');
    Route::put('purchase-order/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->name('purchase_order.update');
    Route::delete('purchase-order/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('purchase_order.destroy');
    Route::post('purchase-order/{purchaseOrder}/request', [PurchaseOrderController::class, 'requestApproval'])->name('purchase_order.request');
    Route::post('purchase-order/{purchaseOrder}/review/{stage}', [PurchaseOrderController::class, 'review'])
        ->whereIn('stage', ['akuntan', 'verval', 'head'])
        ->name('purchase_order.review');
    Route::get('item', [ItemController::class, 'index'])->name('item');
    Route::get('item-vendor', [ItemVendorController::class, 'index'])->name('item_vendor');
    Route::get('menu-item', [MenuController::class, 'index'])->name('item_menu');
    Route::get('purchase-order', [PurchaseOrderController::class, 'index'])->name('purchase_order');
    Route::resource('item', ItemController::class)->except(['index'])->names([
        'create' => 'item.create',
        'store' => 'item.store',
        'show' => 'item.show',
        'edit' => 'item.edit',
        'update' => 'item.update',
        'destroy' => 'item.destroy',
    ]);
    Route::resource('menu-item', MenuController::class)->except(['index'])->names([
        'create' => 'item_menu.create',
        'store' => 'item_menu.store',
        'show' => 'item_menu.show',
        'edit' => 'item_menu.edit',
        'update' => 'item_menu.update',
        'destroy' => 'item_menu.destroy',
    ]);
    Route::resource('item-vendor', ItemVendorController::class)->only(['store', 'edit', 'update', 'destroy'])->names([
        'store' => 'item_vendor.store',
        'edit' => 'item_vendor.edit',
        'update' => 'item_vendor.update',
        'destroy' => 'item_vendor.destroy',
    ]);

});

require __DIR__.'/auth.php';
