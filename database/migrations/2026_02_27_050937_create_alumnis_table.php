<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumnis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->nullable()->constrained('semesters')->nullOnDelete();
            $table->string('siswa_id')->nullable(); // referensi ke siswas.id jika dari dapodik
            $table->string('peserta_didik_id')->nullable();

            // Identitas
            $table->string('nisn')->nullable();
            $table->string('nipd')->nullable();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('nik')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama')->nullable();

            // Akademik
            $table->string('nama_rombel')->nullable();
            $table->string('tingkat_pendidikan_id')->nullable();
            $table->string('kurikulum')->nullable();
            $table->string('sekolah_asal')->nullable();

            // Kelulusan
            $table->string('tahun_lulus');
            $table->date('tanggal_lulus')->nullable();
            $table->string('no_ijazah')->nullable();
            $table->string('no_skhun')->nullable();
            $table->decimal('nilai_rata', 5, 2)->nullable();
            $table->string('keterangan')->nullable();

            // Orang tua
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('nama_wali')->nullable();
            $table->string('no_hp_ortu')->nullable();

            // Kontak
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();

            $table->string('sumber_data')->default('dapodik'); // dapodik | manual | excel
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumnis');
    }
};
