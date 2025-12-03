<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('koreksi_barcode', function (Blueprint $table) {
            $table->id();
            $table->foreignId('igi_id')->constrained('igi')->onDelete('cascade');
            $table->string('nama_barang_lama');
            $table->string('nama_barang_baru');
            $table->string('type_lama');
            $table->string('type_baru');
            $table->dateTime('tanggal_koreksi');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index('igi_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('koreksi_barcode');
    }
};