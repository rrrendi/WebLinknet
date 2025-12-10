<?php
// database/migrations/2025_01_create_activity_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('igi_detail_id')->constrained('igi_details')->onDelete('cascade');
            $table->enum('aktivitas', [
                'IGI',
                'UJI_FUNGSI',
                'REPAIR',
                'REKONDISI',
                'SERVICE_HANDLING',
                'PACKING',
                'KOREKSI'
            ])->index();
            $table->dateTime('tanggal')->index();
            $table->enum('result', ['OK', 'NOK', 'N/A'])->default('N/A')->index();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('keterangan')->nullable();
            
            // Untuk tracking perubahan data (koreksi barcode)
            $table->json('data_lama')->nullable();
            $table->json('data_baru')->nullable();
            
            $table->timestamps();
            
            // Index untuk performa query
            $table->index(['igi_detail_id', 'aktivitas', 'tanggal'], 'idx_detail_aktivitas');
            $table->index(['user_id', 'aktivitas'], 'idx_user_aktivitas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};