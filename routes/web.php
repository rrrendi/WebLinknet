<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IgiController;
use App\Http\Controllers\KoreksiBarcodeController;
use App\Http\Controllers\UjiFungsiController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\RekondisiController;
use App\Http\Controllers\ServiceHandlingController;
use App\Http\Controllers\PackingController;
use App\Http\Controllers\DownloadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // IGI Routes
    Route::resource('igi', IgiController::class);
    Route::get('igi/serial/{serial_number}', [IgiController::class, 'getBySerial'])->name('igi.by-serial');

    // Koreksi Barcode Routes
    Route::get('koreksi-barcode', [KoreksiBarcodeController::class, 'index'])->name('koreksi-barcode.index');
    Route::post('koreksi-barcode/search', [KoreksiBarcodeController::class, 'search'])->name('koreksi-barcode.search');
    Route::get('koreksi-barcode/tracking/{serial_number}', [KoreksiBarcodeController::class, 'tracking'])->name('koreksi-barcode.tracking');
    Route::post('koreksi-barcode/update-data', [KoreksiBarcodeController::class, 'updateData'])->name('koreksi-barcode.update-data');
    Route::get('koreksi-barcode/history', [KoreksiBarcodeController::class, 'history'])->name('koreksi-barcode.history');

    // Uji Fungsi Routes
    Route::get('uji-fungsi', [UjiFungsiController::class, 'index'])->name('uji-fungsi.index');
    Route::post('uji-fungsi/store', [UjiFungsiController::class, 'store'])->name('uji-fungsi.store');
    Route::post('uji-fungsi/check-serial', [UjiFungsiController::class, 'checkSerial'])->name('uji-fungsi.check-serial');
    Route::delete('uji-fungsi/{id}', [UjiFungsiController::class, 'destroy'])->name('uji-fungsi.destroy');
    Route::get('uji-fungsi/monitoring', [UjiFungsiController::class, 'getMonitoring'])->name('uji-fungsi.monitoring');

    // Repair Routes
    Route::get('repair', [RepairController::class, 'index'])->name('repair.index');
    Route::post('repair/store', [RepairController::class, 'store'])->name('repair.store');
    Route::post('repair/check-serial', [RepairController::class, 'checkSerial'])->name('repair.check-serial');
    Route::delete('repair/{id}', [RepairController::class, 'destroy'])->name('repair.destroy');
    Route::get('repair/monitoring', [RepairController::class, 'getMonitoring'])->name('repair.monitoring');

    // Rekondisi Routes
    Route::get('rekondisi', [RekondisiController::class, 'index'])->name('rekondisi.index');
    Route::post('rekondisi/store', [RekondisiController::class, 'store'])->name('rekondisi.store');
    Route::post('rekondisi/check-serial', [RekondisiController::class, 'checkSerial'])->name('rekondisi.check-serial');
    Route::delete('rekondisi/{id}', [RekondisiController::class, 'destroy'])->name('rekondisi.destroy');
    Route::get('rekondisi/monitoring', [RekondisiController::class, 'getMonitoring'])->name('rekondisi.monitoring');

    // Service Handling Routes
    Route::get('service-handling', [ServiceHandlingController::class, 'index'])->name('service-handling.index');
    Route::post('service-handling/store', [ServiceHandlingController::class, 'store'])->name('service-handling.store');
    Route::post('service-handling/check-serial', [ServiceHandlingController::class, 'checkSerial'])->name('service-handling.check-serial');
    Route::delete('service-handling/{id}', [ServiceHandlingController::class, 'destroy'])->name('service-handling.destroy');
    Route::get('service-handling/monitoring', [ServiceHandlingController::class, 'getMonitoring'])->name('service-handling.monitoring');
    Route::get('service-handling/nok-data', [ServiceHandlingController::class, 'getNokData'])->name('service-handling.nok-data');

    // Packing Routes
    Route::get('packing', [PackingController::class, 'index'])->name('packing.index');
    Route::post('packing/store', [PackingController::class, 'store'])->name('packing.store');
    Route::post('packing/check-serial', [PackingController::class, 'checkSerial'])->name('packing.check-serial');
    // 🔥 Aksi 1: Rollback (kembalikan barang ke Rekondisi)
    Route::post('packing/{id}/rollback', [PackingController::class, 'rollback'])->name('packing.rollback');
    // 🔥 Aksi 2: Full Delete (hapus seluruh proses & reset IGI)
    Route::post('packing/{id}/full-delete', [PackingController::class, 'fullDelete'])->name('packing.full-delete');
    // destroy DIPERTAHANKAN (mengarah ke rollback)
    Route::delete('packing/{id}', [PackingController::class, 'destroy'])->name('packing.destroy');
    Route::get('packing/monitoring', [PackingController::class, 'getMonitoring'])->name('packing.monitoring');

    // Download Routes
    Route::get('download', [DownloadController::class, 'index'])->name('download.index');
    Route::post('download/export', [DownloadController::class, 'export'])->name('download.export');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
