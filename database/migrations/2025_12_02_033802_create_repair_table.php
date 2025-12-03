<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair', function (Blueprint $table) {
            $table->id();
            $table->foreignId('igi_id')->constrained('igi')->onDelete('cascade');
            $table->enum('status', ['OK', 'NOK']);
            $table->string('jenis_kerusakan');
            $table->text('tindakan')->nullable();
            $table->dateTime('waktu_repair');
            $table->timestamps();
            
            $table->index('igi_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair');
    }
};