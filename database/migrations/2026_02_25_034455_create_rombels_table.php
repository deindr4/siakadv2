<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rombels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->nullable()->constrained()->nullOnDelete();
            $table->string('rombongan_belajar_id')->nullable(); // ID Dapodik
            $table->string('nama_rombel');
            $table->string('tingkat')->nullable(); // X, XI, XII
            $table->string('jurusan')->nullable();
            $table->string('kurikulum')->nullable();
            $table->integer('jumlah_siswa')->default(0);
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('rombels'); }
};
