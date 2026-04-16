<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained('semesters');
            $table->foreignId('rombongan_belajar_id')->constrained('rombels');
            $table->string('nama_rombel');
            $table->date('tanggal');

            // Guru yang melakukan absensi
            $table->foreignId('guru_id')->nullable()->constrained('gurus');
            $table->string('nama_guru')->nullable(); // log nama guru
            $table->timestamp('diabsen_pada')->nullable(); // jam absen
            $table->string('ip_address', 45)->nullable(); // log IP

            // Status kunci
            $table->boolean('is_locked')->default(false);
            $table->foreignId('locked_by')->nullable()->constrained('users'); // admin yg unlock
            $table->timestamp('locked_at')->nullable();

            $table->text('catatan')->nullable();
            $table->timestamps();

            // Satu kelas hanya boleh 1 absensi per hari
            $table->unique(['rombongan_belajar_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_harian');
    }
};
