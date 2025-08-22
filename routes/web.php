<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\KorlapController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ManufactController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Public Routes (Guest Only) ---
Route::middleware('guest')->group(function () {
    // Root redirect to login
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    
    // Authentication routes
    Route::post('/login/korlap', [LoginController::class, 'loginKorlap'])->name('login.korlap');
    Route::post('/login/admin', [LoginController::class, 'loginAdmin'])->name('login.admin');
});

// --- API Routes (Public for frontend functionality) ---
Route::prefix('api')->name('api.')->group(function () {
    Route::post('/get-sap-user-id', [LoginController::class, 'getSapUserByKode'])->name('get_sap_user_id');
});
Route::post('/create_prod_order', [ManufactController::class, 'convertPlannedOrder'])->name('convert-button');

// --- Authenticated Routes ---
Route::middleware('auth')->group(function () {
    
    // Home redirect based on role
    Route::get('/home', function () {
        return match (Auth::user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'korlap' => redirect()->route('korlap.dashboard'),
            default => redirect()->route('login')
        };
    })->name('home');

    // Alternative dashboard route (fallback)
    Route::get('/dashboard', function () {
        return redirect()->route('home');
    })->name('dashboard');

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('data2/{kode}', [ManufactController::class, 'DetailData2'])->name('detail.data2');
    Route::get('data2/detail/{kode}', [ManufactController::class, 'showDetail'])->name('show.detail.data2');
    Route::match(['get', 'post'], 'data/refresh', [ManufactController::class, 'dataRefresh'])->name('data.refresh');

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