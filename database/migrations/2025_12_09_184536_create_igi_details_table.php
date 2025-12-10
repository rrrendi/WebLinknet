<?php
// database/migrations/2025_01_create_igi_details_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('igi_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bapb_id')->constrained('igi_bapb')->onDelete('cascade');
            $table->enum('jenis', ['STB', 'ONT', 'ROUTER'])->index();
            $table->string('merk', 100)->index();
            $table->string('type', 100);
            $table->string('serial_number', 100)->unique()->index(); // UNIQUE & INDEX
            $table->string('mac_address', 50)->nullable()->index();
            $table->string('stb_id', 100)->nullable(); // Khusus STB
            $table->dateTime('scan_time')->index();
            $table->foreignId('scan_by')->constrained('users')->onDelete('cascade');
            
            // Status untuk tracking proses
            $table->enum('status_proses', [
                'IGI',
                'UJI_FUNGSI',
                'REPAIR',
                'REKONDISI',
                'SERVICE_HANDLING',
                'PACKING'
            ])->default('IGI')->index();
            
            $table->timestamps();
            $table->softDeletes(); // Soft delete untuk history
            
            // Composite indexes untuk performa
            $table->index(['bapb_id', 'jenis'], 'idx_bapb_jenis');
            $table->index(['jenis', 'merk', 'type'], 'idx_jenis_merk_type');
            $table->index(['status_proses', 'jenis'], 'idx_status_jenis');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('igi_details');
    }
};