<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_handling', function (Blueprint $table) {
            $table->id();
            $table->foreignId('igi_id')->constrained('igi')->onDelete('cascade');
            $table->enum('sumber', ['UJI_FUNGSI', 'REPAIR']);
            $table->enum('status', ['NOK'])->default('NOK');
            $table->text('keterangan')->nullable();
            $table->dateTime('waktu_service');
            $table->timestamps();
            
            $table->index('igi_id');
            $table->index('sumber');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_handling');
    }
};