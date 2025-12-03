<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uji_fungsi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('igi_id')->constrained('igi')->onDelete('cascade');
            $table->enum('status', ['OK', 'NOK']);
            $table->text('keterangan')->nullable();
            $table->dateTime('waktu_uji');
            $table->timestamps();
            
            $table->index('igi_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uji_fungsi');
    }
};