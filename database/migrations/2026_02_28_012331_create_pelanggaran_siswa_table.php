<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggaran_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('jenis_pelanggaran_id')->constrained('jenis_pelanggaran')->cascadeOnDelete();
            $table->foreignId('dicatat_oleh')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal');
            $table->integer('poin'); // poin saat dicatat (snapshot)
            $table->text('keterangan')->nullable();
            $table->string('tindakan')->nullable(); // tindakan yang diambil
            $table->enum('status', ['aktif', 'selesai', 'dibatalkan'])->default('aktif');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggaran_siswa');
    }
};
