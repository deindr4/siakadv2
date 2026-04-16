<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestasi', function (Blueprint $table) {
            $table->id();

            // Relasi
            $table->foreignId('kategori_id')
                  ->nullable()
                  ->constrained('kategori_prestasi')
                  ->nullOnDelete();

            $table->foreignId('semester_id')
                  ->nullable()
                  ->constrained('semesters')
                  ->nullOnDelete();

            // Info lomba / kegiatan
            $table->string('nama_lomba');
            $table->string('penyelenggara')->nullable();
            $table->enum('tingkat', [
                'sekolah', 'kecamatan', 'kabupaten',
                'provinsi', 'nasional', 'internasional'
            ]);
            $table->date('tanggal');
            $table->string('tempat')->nullable();

            // Hasil
            $table->string('juara');                     // bebas isi: Juara 1, Medali Emas, Finalis, dll
            $table->integer('juara_urut')->default(99);  // 1=Juara1, 2=Juara2, 99=lainnya (untuk sorting)

            // Tipe
            $table->enum('tipe', ['individu', 'tim'])->default('individu');
            $table->string('nama_tim')->nullable(); // diisi jika tipe = tim

            // Verifikasi
            $table->enum('status', ['pending', 'diverifikasi', 'ditolak'])->default('pending');
            $table->foreignId('diverifikasi_oleh')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('diverifikasi_pada')->nullable();
            $table->text('catatan_verifikasi')->nullable();

            // Pembuat
            $table->foreignId('dibuat_oleh')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->string('role_pembuat')->nullable(); // admin|guru|siswa|bk|tata_usaha

            // Bukti sertifikat
            $table->string('file_sertifikat')->nullable();          // path di storage/public
            $table->string('file_sertifikat_original')->nullable(); // nama file asli dari user

            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasi');
    }
};
