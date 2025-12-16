<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    IgiController,
    UjiFungsiController,
    RepairController,
    RekondisiController,
    ServiceHandlingController,
    PackingController,
    KoreksiBarcodeController,
    DownloadController,
    UserManagementController,
    ProfileController
};

// Redirect root ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// ========================================
// ROUTES UNTUK SEMUA AUTHENTICATED USER (termasuk Tamu)
// ========================================
Route::middleware(['auth', 'active'])->group(function () {
    
    // DASHBOARD - Semua role bisa akses
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // DOWNLOAD DATA - Semua role bisa akses
    Route::prefix('download')->name('download.')->group(function () {
        Route::get('/', [DownloadController::class, 'index'])->name('index');
        Route::post('/export', [DownloadController::class, 'export'])->name('export');
        Route::get('/wilayah-by-pemilik', [DownloadController::class, 'getWilayahByPemilik'])->name('wilayah-by-pemilik');
    });

    // PROFILE - Semua role bisa akses
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

});

// ========================================
// ROUTES UNTUK ADMIN & USER ONLY (BLOK TAMU)
// ========================================
Route::middleware(['auth', 'active', 'not.tamu'])->group(function () {
    
    // ==========================================
    // IGI ROUTES
    // ==========================================
    Route::prefix('igi')->name('igi.')->group(function () {
        // List BAPB Header
        Route::get('/', [IgiController::class, 'index'])->name('index');
        
        // Create BAPB Header
        Route::get('/create', [IgiController::class, 'create'])->name('create');
        Route::post('/store-bapb', [IgiController::class, 'storeBapb'])->name('store-bapb');
        
        // Confirm BAPB
        Route::get('/{bapbId}/confirm', [IgiController::class, 'confirmBapb'])->name('confirm-bapb');
        
        // Edit BAPB Header
        Route::get('/{id}/edit', [IgiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [IgiController::class, 'update'])->name('update');
        
        // Delete BAPB Header
        Route::delete('/{id}', [IgiController::class, 'destroy'])->name('destroy');
        
        // Scan Detail Barang
        Route::get('/{bapbId}/scan', [IgiController::class, 'scanDetail'])->name('scan-detail');
        Route::post('/store-detail', [IgiController::class, 'storeDetail'])->name('store-detail');
        Route::delete('/detail/{id}', [IgiController::class, 'deleteDetail'])->name('delete-detail');
        
        // API untuk dropdown
        Route::get('/api/merk/{jenis}', [IgiController::class, 'getMerkByJenis'])->name('api.merk');
        Route::get('/api/type/{merkId}', [IgiController::class, 'getTypeByMerk'])->name('api.type');
    });

    // ==========================================
    // UJI FUNGSI ROUTES
    // ==========================================
    Route::prefix('uji-fungsi')->name('uji-fungsi.')->group(function () {
        Route::get('/', [UjiFungsiController::class, 'index'])->name('index');
        Route::post('/check-serial', [UjiFungsiController::class, 'checkSerial'])->name('check-serial');
        Route::post('/store', [UjiFungsiController::class, 'store'])->name('store');
        Route::delete('/{id}', [UjiFungsiController::class, 'destroy'])->name('destroy');
    });

    // ==========================================
    // REPAIR ROUTES
    // ==========================================
    Route::prefix('repair')->name('repair.')->group(function () {
        Route::get('/', [RepairController::class, 'index'])->name('index');
        Route::post('/check-serial', [RepairController::class, 'checkSerial'])->name('check-serial');
        Route::post('/store', [RepairController::class, 'store'])->name('store');
        Route::delete('/{id}', [RepairController::class, 'destroy'])->name('destroy');
    });

    // ==========================================
    // REKONDISI ROUTES
    // ==========================================
    Route::prefix('rekondisi')->name('rekondisi.')->group(function () {
        Route::get('/', [RekondisiController::class, 'index'])->name('index');
        Route::post('/check-serial', [RekondisiController::class, 'checkSerial'])->name('check-serial');
        Route::post('/store', [RekondisiController::class, 'store'])->name('store');
        Route::delete('/{id}', [RekondisiController::class, 'destroy'])->name('destroy');
    });

    // ==========================================
    // SERVICE HANDLING ROUTES
    // ==========================================
    Route::prefix('service-handling')->name('service-handling.')->group(function () {
        Route::get('/', [ServiceHandlingController::class, 'index'])->name('index');
        Route::post('/check-serial', [ServiceHandlingController::class, 'checkSerial'])->name('check-serial');
        Route::post('/store', [ServiceHandlingController::class, 'store'])->name('store');
        Route::delete('/{id}', [ServiceHandlingController::class, 'destroy'])->name('destroy');
    });

    // ==========================================
    // PACKING ROUTES
    // ==========================================
    Route::prefix('packing')->name('packing.')->group(function () {
        Route::get('/', [PackingController::class, 'index'])->name('index');
        Route::post('/check-serial', [PackingController::class, 'checkSerial'])->name('check-serial');
        Route::post('/store', [PackingController::class, 'store'])->name('store');
        Route::delete('/{id}', [PackingController::class, 'destroy'])->name('destroy');
    });

    // ==========================================
    // KOREKSI BARCODE ROUTES
    // ==========================================
    Route::prefix('koreksi-barcode')->name('koreksi-barcode.')->group(function () {
        Route::get('/', [KoreksiBarcodeController::class, 'index'])->name('index');
        Route::post('/search', [KoreksiBarcodeController::class, 'search'])->name('search');
        Route::get('/{id}/activity', [KoreksiBarcodeController::class, 'getActivityHistory'])->name('activity');
        Route::put('/{id}/update', [KoreksiBarcodeController::class, 'update'])->name('update');
        Route::delete('/activity/{id}', [KoreksiBarcodeController::class, 'deleteActivity'])->name('delete-activity');
    });

});

// ========================================
// ADMIN ONLY ROUTES
// ========================================
Route::middleware(['auth', 'active', 'admin'])->group(function () {
    
    // ==========================================
    // USER MANAGEMENT ROUTES (ADMIN ONLY)
    // ==========================================
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/create', [UserManagementController::class, 'create'])->name('create');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [UserManagementController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserManagementController::class, 'destroy'])->name('destroy');
    });
    
});

// Include auth routes (login, register, etc)
require __DIR__.'/auth.php';