<?php
// database/migrations/2025_01_create_igi_bapb_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('igi_bapb', function (Blueprint $table) {
            $table->id();
            $table->enum('pemilik', ['Linknet', 'Telkomsel'])->index();
            $table->date('tanggal_datang')->index();
            $table->string('no_ido', 50)->unique(); // No. IDO/BAPB
            $table->string('wilayah', 100)->index();
            $table->integer('jumlah')->default(0); // Total BAPB
            $table->integer('total_scan')->default(0); // Total barang ter-scan
            $table->timestamps();
            
            // Composite index untuk performa query filter
            $table->index(['pemilik', 'wilayah', 'tanggal_datang'], 'idx_bapb_filter');
            $table->index(['no_ido', 'pemilik'], 'idx_bapb_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('igi_bapb');
    }
};