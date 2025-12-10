<?php
// database/migrations/2025_01_create_uji_fungsi_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uji_fungsi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('igi_detail_id')->constrained('igi_details')->onDelete('cascade');
            $table->enum('result', ['OK', 'NOK'])->index();
            $table->dateTime('uji_fungsi_time')->index();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Index untuk performa monitoring
            $table->index(['result', 'uji_fungsi_time'], 'idx_result_time');
            $table->index(['user_id', 'uji_fungsi_time'], 'idx_user_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uji_fungsi');
    }
};