<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Master jenis kegiatan positif
        Schema::create('jenis_kegiatan_positif', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('kategori', [
                'akademik',
                'olahraga_seni',
                'organisasi',
                'sosial_keagamaan',
                'lainnya',
            ]);
            $table->unsignedTinyInteger('poin')->default(5); // poin pengurangan
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Catatan poin positif per siswa
        Schema::create('poin_positif_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained('semesters');
            $table->foreignId('siswa_id')->constrained('siswas');
            $table->foreignId('jenis_kegiatan_id')->constrained('jenis_kegiatan_positif');
            $table->foreignId('dicatat_oleh')->constrained('users');
            $table->date('tanggal');
            $table->unsignedTinyInteger('poin'); // snapshot poin saat dicatat
            $table->string('keterangan')->nullable();
            $table->string('bukti')->nullable(); // path file bukti (opsional)
            $table->enum('status', ['aktif', 'dibatalkan'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poin_positif_siswa');
        Schema::dropIfExists('jenis_kegiatan_positif');
    }
};
