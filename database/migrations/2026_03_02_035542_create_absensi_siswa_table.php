<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('absensi_harian_id')->constrained('absensi_harian')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas');
            $table->enum('status', ['H', 'S', 'I', 'A', 'D'])->default('H');
            // H=Hadir, S=Sakit, I=Izin, A=Alpa, D=Dispensasi
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['absensi_harian_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_siswa');
    }
};
