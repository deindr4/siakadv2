<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestasi_siswa', function (Blueprint $table) {
            $table->id();

            $table->foreignId('prestasi_id')
                  ->constrained('prestasi')
                  ->cascadeOnDelete();

            // siswas.id = bigint(20) unsigned AUTO_INCREMENT
            $table->unsignedBigInteger('siswa_id');
            $table->foreign('siswa_id')
                  ->references('id')
                  ->on('siswas')
                  ->cascadeOnDelete();

            $table->string('peran')->nullable(); // kapten, anggota, dll (untuk tipe tim)

            $table->timestamps();

            $table->unique(['prestasi_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasi_siswa');
    }
};
