<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekondisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('igi_id')->constrained('igi')->onDelete('cascade');
            $table->text('tindakan')->nullable();
            $table->dateTime('waktu_rekondisi');
            $table->timestamps();
            
            $table->index('igi_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekondisi');
    }
};