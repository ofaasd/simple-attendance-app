<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController as user;
use App\Http\Controllers\WorkingHourController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\DashboardController;
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

});

require __DIR__.'/auth.php';
