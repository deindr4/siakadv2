<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gurus', function (Blueprint $table) {
            $table->id();

            // Relasi
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Identitas Dapodik
            $table->string('ptk_id')->nullable();         // ID Dapodik
            $table->string('ptk_terdaftar_id')->nullable();
            $table->string('nuptk')->nullable();
            $table->string('nip')->nullable();
            $table->string('nik')->nullable();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama')->nullable();

            // Kepegawaian
            $table->string('jenis_ptk')->nullable();       // Guru, Tendik, dll
            $table->string('jabatan')->nullable();         // Guru Matematika, dll
            $table->string('status_kepegawaian')->nullable(); // PNS, PPPK, GTT
            $table->string('pangkat_golongan')->nullable();
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('bidang_studi')->nullable();
            $table->string('tahun_ajaran')->nullable();
            $table->date('tanggal_surat_tugas')->nullable();

            // Kontak
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();

            // Status
            $table->enum('status', ['aktif', 'tidak_aktif', 'pensiun'])->default('aktif');
            $table->boolean('is_archived')->default(false);
            $table->string('tahun_arsip')->nullable();

            // Sumber data
            $table->enum('sumber_data', ['manual', 'dapodik', 'excel'])->default('manual');
            $table->timestamp('last_sync_dapodik')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void { Schema::dropIfExists('gurus'); }
};
