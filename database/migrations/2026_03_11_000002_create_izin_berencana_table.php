<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('izin_berencana', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_izin')->unique(); // auto-generate IZN-2026-0001
            $table->foreignId('semester_id')->constrained('semesters');
            $table->foreignId('siswa_id')->constrained('siswas');

            // Detail izin
            $table->enum('jenis', ['keperluan_keluarga', 'perjalanan_wisata', 'lainnya']);
            $table->text('alasan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->unsignedTinyInteger('jumlah_hari'); // auto-hitung

            // Verifikasi ortu
            $table->string('nama_ortu');
            $table->string('no_hp_ortu');
            $table->text('ttd_ortu')->nullable(); // base64 signature pad

            // Status & approval
            $table->enum('status', [
                'pending',      // baru diajukan
                'disetujui',    // approved kepsek/admin
                'ditolak',      // rejected
                'dibatalkan',   // dibatalkan siswa
            ])->default('pending');

            $table->foreignId('disetujui_oleh')->nullable()->constrained('users');
            $table->timestamp('disetujui_pada')->nullable();
            $table->unsignedTinyInteger('jumlah_hari_disetujui')->nullable(); // admin bisa override
            $table->text('catatan_approver')->nullable();

            // Lampiran
            $table->string('lampiran')->nullable(); // foto/dokumen pendukung

            $table->timestamps();

            $table->index(['siswa_id', 'status']);
            $table->index(['semester_id', 'status']);
            $table->index(['tanggal_mulai', 'tanggal_selesai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('izin_berencana');
    }
};
