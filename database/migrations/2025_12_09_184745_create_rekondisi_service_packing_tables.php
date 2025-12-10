<?php
// database/migrations/2025_01_create_rekondisi_service_packing_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // REKONDISI
        Schema::create('rekondisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('igi_detail_id')->constrained('igi_details')->onDelete('cascade');
            $table->dateTime('rekondisi_time')->index();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['user_id', 'rekondisi_time'], 'idx_user_time');
        });

        // SERVICE HANDLING
        Schema::create('service_handling', function (Blueprint $table) {
            $table->id();
            $table->foreignId('igi_detail_id')->constrained('igi_details')->onDelete('cascade');
            $table->dateTime('service_time')->index();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'service_time'], 'idx_user_time');
        });

        // PACKING
        Schema::create('packing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('igi_detail_id')->constrained('igi_details')->onDelete('cascade');
            $table->dateTime('packing_time')->index();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('kondisi_box')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'packing_time'], 'idx_user_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packing');
        Schema::dropIfExists('service_handling');
        Schema::dropIfExists('rekondisi');
    }
};