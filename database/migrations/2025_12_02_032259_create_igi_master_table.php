<?php
// ==========================================
// database/migrations/xxxx_create_igi_master_table.php
// TABEL PERMANEN - UNTUK AUDIT & HISTORI
// ==========================================
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('igi_master', function (Blueprint $table) {
            $table->id();
            $table->string('no_do')->unique();
            $table->dateTime('tanggal_datang');
            $table->string('nama_barang');
            $table->string('type');
            $table->string('serial_number')->unique();
            $table->integer('total_scan')->default(0);
            $table->enum('status_proses', [
                'IGI',
                'UJI_FUNGSI',
                'REPAIR',
                'REKONDISI',
                'SERVICE_HANDLING',
                'PACKING'
            ])->default('IGI');
            $table->timestamps();
            
            // Index untuk performa
            $table->index('serial_number');
            $table->index('no_do');
            $table->index('nama_barang');
            $table->index('status_proses');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('igi_master');
    }
};