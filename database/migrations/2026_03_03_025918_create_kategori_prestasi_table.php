<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_prestasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jenis')->default('akademik'); // akademik | non_akademik
            $table->string('warna')->default('#6366f1');  // hex color untuk badge
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_prestasi');
    }
};
