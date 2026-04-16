<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_pelanggaran', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // P001, P002, dst
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->enum('kategori', ['ringan', 'sedang', 'berat'])->default('ringan');
            $table->integer('poin')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_pelanggaran');
    }
};
