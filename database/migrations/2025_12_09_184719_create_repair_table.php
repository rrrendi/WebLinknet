<?php
// database/migrations/2025_01_create_repair_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair', function (Blueprint $table) {
            $table->id();
            $table->foreignId('igi_detail_id')->constrained('igi_details')->onDelete('cascade');
            $table->enum('jenis_kerusakan', ['Masih Hidup', 'Mati Total'])->index();
            $table->enum('result', ['OK', 'NOK'])->index();
            $table->dateTime('repair_time')->index();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            // Index untuk monitoring dan filter
            $table->index(['result', 'jenis_kerusakan'], 'idx_result_kerusakan');
            $table->index(['user_id', 'repair_time'], 'idx_user_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair');
    }
};