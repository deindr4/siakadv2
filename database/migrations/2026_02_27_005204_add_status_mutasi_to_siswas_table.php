<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('status_mutasi')->default('aktif')->after('status');
            // aktif | mutasi_masuk | mutasi_keluar | putus_sekolah | lulus | berhenti
            $table->text('keterangan_mutasi')->nullable()->after('status_mutasi');
            $table->date('tanggal_mutasi')->nullable()->after('keterangan_mutasi');
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn(['status_mutasi', 'keterangan_mutasi', 'tanggal_mutasi']);
        });
    }
};
