<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->string('semester_id')->unique(); // "20252"
            $table->string('nama');                  // "Semester 2 2025/2026"
            $table->string('tahun_ajaran');          // "2025/2026"
            $table->enum('tipe', ['ganjil', 'genap']);
            $table->boolean('is_aktif')->default(false);
            $table->timestamp('tanggal_mulai')->nullable();
            $table->timestamp('tanggal_selesai')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('semesters'); }
};
