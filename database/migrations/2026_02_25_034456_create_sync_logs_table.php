<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('jenis'); // siswa, guru, rombel, sekolah, semester, semua
            $table->string('semester_id')->nullable();
            $table->integer('total_data')->default(0);
            $table->integer('created')->default(0);
            $table->integer('updated')->default(0);
            $table->integer('archived')->default(0);
            $table->integer('failed')->default(0);
            $table->json('errors')->nullable();
            $table->enum('status', ['sukses', 'gagal', 'sebagian'])->default('sukses');
            $table->integer('durasi_detik')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('sync_logs'); }
};
