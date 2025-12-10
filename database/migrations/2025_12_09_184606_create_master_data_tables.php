<?php
// database/migrations/2025_01_create_master_data_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Master Merk berdasarkan Jenis
        Schema::create('master_merk', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['STB', 'ONT', 'ROUTER'])->index();
            $table->string('merk', 100)->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['jenis', 'merk'], 'unique_jenis_merk');
        });

        // Master Type berdasarkan Jenis dan Merk
        Schema::create('master_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merk_id')->constrained('master_merk')->onDelete('cascade');
            $table->string('type', 100)->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['merk_id', 'type'], 'unique_merk_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_type');
        Schema::dropIfExists('master_merk');
    }
};