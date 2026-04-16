<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_mengajar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->cascadeOnDelete();
            $table->string('nama_rombel');
            $table->string('rombongan_belajar_id')->nullable();
            $table->date('tanggal');
            $table->string('jam_ke')->nullable();       // misal: 1-2, 3-4
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->integer('pertemuan_ke')->nullable();
            $table->text('materi');
            $table->text('kegiatan');
            $table->text('catatan')->nullable();
            $table->integer('jumlah_hadir')->nullable();
            $table->integer('jumlah_tidak_hadir')->nullable();
            $table->string('foto_pendukung')->nullable();
            $table->text('tanda_tangan')->nullable();   // base64 signature
            $table->enum('status', ['submitted'])->default('submitted');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_mengajar');
    }
};
