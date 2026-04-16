<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();

            // Relasi
            $table->foreignId('semester_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Identitas
            $table->string('nisn')->nullable();
            $table->string('nipd')->nullable();
            $table->string('peserta_didik_id')->nullable(); // ID Dapodik
            $table->string('registrasi_id')->nullable();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('nik')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama')->nullable();

            // Kontak
            $table->string('no_hp')->nullable();
            $table->string('no_hp_rumah')->nullable();
            $table->string('email')->nullable();

            // Fisik
            $table->string('tinggi_badan')->nullable();
            $table->string('berat_badan')->nullable();
            $table->string('kebutuhan_khusus')->nullable();

            // Orang Tua
            $table->string('nama_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('nama_wali')->nullable();
            $table->string('pekerjaan_wali')->nullable();
            $table->string('no_hp_ortu')->nullable();
            $table->string('anak_keberapa')->nullable();

            // Akademik
            $table->string('nama_rombel')->nullable();
            $table->string('rombongan_belajar_id')->nullable();
            $table->string('tingkat_pendidikan_id')->nullable(); // 10=X, 11=XI, 12=XII
            $table->string('kurikulum')->nullable();
            $table->string('sekolah_asal')->nullable();
            $table->date('tanggal_masuk_sekolah')->nullable();
            $table->string('jenis_pendaftaran')->nullable();

            // Status
            $table->enum('status', ['aktif', 'lulus', 'keluar', 'pindah'])->default('aktif');
            $table->boolean('is_archived')->default(false);
            $table->string('semester_arsip')->nullable(); // semester_id saat diarsipkan

            // Sumber data
            $table->enum('sumber_data', ['manual', 'dapodik', 'excel'])->default('manual');
            $table->timestamp('last_sync_dapodik')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void { Schema::dropIfExists('siswas'); }
};
