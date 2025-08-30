<?php

use App\Http\Controllers\Data3Controller;
use App\Http\Controllers\Data1Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\KorlapController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Data4Controller;
use App\Http\Controllers\ManufactController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Routing untuk user yang belum melakukan register
Route::middleware('guest')->group(function (){

    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    // Routing untuk login masing-masing role
    Route::post('/login/korlap', [LoginController::class, 'loginKorlap'])->name('login.korlap');
    Route::post('/login/admin', [LoginController::class, 'loginAdmin'])->name('login.admin');
    // routing untuk mendapatkan sap_id otomatis ketika korlap login
    Route::post('api/get-sap-user-id', [LoginController::class, 'getSapUserByKode'])->name('get_sap_user_id');
    
});

Route::middleware('auth')->group(function (){

    // Routing Admin
    Route::get('/dashboard-landing', [AdminController::class, 'AdminDashboard'])->name('dashboard-landing');
    Route::get('/dashboard/{kode}', [AdminController::class, 'index'])->name('dashboard.show');
    Route::get('data2/{kode}', [ManufactController::class, 'DetailData2'])->name('detail.data2');
    Route::get('data2/detail/{kode}', [ManufactController::class, 'showDetail'])->name('show.detail.data2');

    // Routing Korlap

    // Routing Manufact
    Route::post('/create_prod_order', [ManufactController::class, 'convertPlannedOrder'])->name('convert-button');
    Route::post('/component/add', [Data4Controller::class, 'addComponent'])->name('component.add');
    Route::post('/component/delete-bulk', [Data4Controller::class, 'deleteBulkComponents'])->name('component.delete.bulk');

    // Routing Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // route untuk kelola T-DATA3
    Route::get('/release-order/{aufnr}',[Data3Controller::class, 'releaseOrderDirect'])->name('release.order.direct');
    Route::post('/reschedule', [Data3Controller::class,'reschedule'])->name('reschedule.store');
    Route::post('/teco-order', [Data3Controller::class, 'tecoOrder'])->name('order.teco');
    Route::post('/read-pp-order', [Data3Controller::class, 'readPpOrder'])->name('order.readpp');

    // route untuk kelola T-DATA1
    Route::post('/changeWC', [Data1Controller::class,'changeWC'])->name('change-wc');
    Route::post('/changePV', [Data1Controller::class,'changePV'])->name('change-pv');

});






















// --- Public Routes (Guest Only) ---
Route::middleware('guest')->group(function () {
    // Root redirect to login
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    
    // Authentication routes
    Route::post('/login/korlap', [LoginController::class, 'loginKorlap'])->name('login.korlap');
    Route::post('/login/admin', [LoginController::class, 'loginAdmin'])->name('login.admin');
});



// route untuk kelola menu admin
Route::get('/dashboard_all', [AdminController::class, 'AdminDashboard'])->name('dashboard_all');
Route::get('/dashboard/{kode}', [AdminController::class, 'index'])->name('dashboard.show');
Route::get('data2/{kode}', [ManufactController::class, 'DetailData2'])->name('detail.data2');
Route::get('data2/detail/{kode}', [ManufactController::class, 'showDetail'])->name('show.detail.data2');






// --- Authenticated Routes ---
Route::middleware('auth')->group(function () {
    
    // Home redirect based on role
    Route::get('/home', function () {
        return match (Auth::user()->role) {
            'admin' => redirect()->route('dashboard_all'),
            'korlap' => redirect()->route('korlap.dashboard'),
            default => redirect()->route('login')
        };
    })->name('home');

    // Logout
    
    // Route::match(['get', 'post'], 'data/refresh', [ManufactController::class, 'dataRefresh'])->name('data.refresh');

    // --- Admin Routes ---
    Route::middleware(['role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/dashboard', action: [AdminController::class, 'index'])->name('dashboard');
        });

    // --- Korlap Routes ---
    Route::middleware(['role:korlap'])
        ->prefix('korlap')
        ->name('korlap.')
        ->group(function () {
            Route::get('/dashboard', [KorlapController::class, 'index'])->name('dashboard');
            Route::get('/tasks', [KorlapController::class, 'tasks'])->name('tasks');
            Route::post('/tasks/{id}/update', [KorlapController::class, 'updateTask'])->name('tasks.update');
        });
});

// --- Fallback Route ---
Route::fallback(function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return redirect()->route('login');
});