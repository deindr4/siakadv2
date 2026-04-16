<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name')->nullable();        // nama user saat aksi
            $table->string('role')->nullable();        // role user saat aksi
            $table->string('action');                  // create, update, delete, login, logout, export
            $table->string('module');                  // siswa, guru, absensi, pelanggaran, dll
            $table->string('description');             // deskripsi singkat aksi
            $table->string('model_type')->nullable();  // App\Models\Siswa
            $table->unsignedBigInteger('model_id')->nullable(); // id record yang diubah
            $table->json('old_values')->nullable();    // data sebelum diubah
            $table->json('new_values')->nullable();    // data setelah diubah
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['module', 'action']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
