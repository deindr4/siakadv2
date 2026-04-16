<?php
// database/migrations/xxxx_create_tikets_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tikets', function (Blueprint $table) {
            $table->id();

            // Pembuat tiket
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role_pembuat');         // siswa | guru
            $table->boolean('is_anonim')->default(false); // anonim ke sesama, admin/kepsek tetap tahu

            // Isi tiket
            $table->string('judul');
            $table->enum('kategori', [
                'kritik',
                'saran',
                'pengaduan_fasilitas',
                'pengaduan_guru_staff',
                'pengaduan_teman_bullying',
                'lainnya'
            ]);
            $table->string('kategori_lainnya')->nullable(); // isi jika kategori = lainnya
            $table->text('isi');
            $table->string('foto')->nullable();             // path foto di storage
            $table->string('foto_original')->nullable();   // nama file asli

            // Status & prioritas
            $table->enum('status', ['open', 'diproses', 'selesai', 'terkunci'])->default('open');
            $table->enum('prioritas', ['rendah', 'sedang', 'tinggi'])->default('sedang');

            // Auto-lock: tiket terkunci jika tidak ada respon 7 hari
            $table->timestamp('last_response_at')->nullable(); // last activity
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('unlocked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('unlocked_at')->nullable();
            $table->text('alasan_unlock')->nullable();

            // Ditutup oleh
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tiket_respon', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_id')->constrained('tikets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role_responder');   // siswa | guru | admin | kepala_sekolah
            $table->boolean('is_anonim')->default(false);
            $table->text('isi');
            $table->string('foto')->nullable();
            $table->string('foto_original')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiket_respon');
        Schema::dropIfExists('tikets');
    }
};
