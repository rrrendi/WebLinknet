<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('igi_id')->constrained('igi')->onDelete('cascade');
            $table->dateTime('waktu_packing');
            $table->string('kondisi_box')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            $table->index('igi_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packing');
    }
};