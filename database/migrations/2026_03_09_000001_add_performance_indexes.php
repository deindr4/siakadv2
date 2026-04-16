<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── absensi_siswa ──────────────────────────────────────────
        // Query: WHERE siswa_id + status (rekap absensi siswa di dashboard)
        Schema::table('absensi_siswa', function (Blueprint $table) {
            $table->index(['siswa_id', 'status'], 'idx_absensi_siswa_siswa_status');
        });

        // ── absensi_harian ─────────────────────────────────────────
        // Query: WHERE DATE(tanggal) + semester_id (paling sering)
        // Query: WHERE guru_id + DATE(tanggal) (dashboard guru)
        Schema::table('absensi_harian', function (Blueprint $table) {
            $table->index(['tanggal', 'semester_id'], 'idx_absensi_harian_tgl_sem');
            $table->index(['guru_id', 'tanggal'],     'idx_absensi_harian_guru_tgl');
        });

        // ── pelanggaran_siswa ──────────────────────────────────────
        // Query: WHERE semester_id + DATE(tanggal) (dashboard BK/wakilkepala)
        // Query: WHERE siswa_id + semester_id (dashboard siswa)
        // Query: GROUP BY siswa_id + SUM(poin) (top poin)
        Schema::table('pelanggaran_siswa', function (Blueprint $table) {
            $table->index(['semester_id', 'tanggal'],  'idx_pel_siswa_sem_tgl');
            $table->index(['siswa_id', 'semester_id'], 'idx_pel_siswa_siswa_sem');
        });

        // ── prestasi ───────────────────────────────────────────────
        // Query: WHERE semester_id + status (paling sering, semua dashboard)
        // Query: WHERE semester_id + status + tingkat (kepala sekolah)
        // Query: WHERE dibuat_oleh + semester_id (dashboard guru)
        Schema::table('prestasi', function (Blueprint $table) {
            $table->index(['semester_id', 'status'],          'idx_prestasi_sem_status');
            $table->index(['semester_id', 'status', 'tingkat'], 'idx_prestasi_sem_status_tingkat');
            $table->index(['dibuat_oleh', 'semester_id'],     'idx_prestasi_dibuat_sem');
        });

        // ── tikets ─────────────────────────────────────────────────
        // Query: WHERE status IN (...) (semua dashboard)
        // Query: WHERE user_id + status (dashboard guru/siswa)
        // Query: WHERE status + prioritas (dashboard admin/kepala)
        Schema::table('tikets', function (Blueprint $table) {
            $table->index(['status'],             'idx_tikets_status');
            $table->index(['user_id', 'status'],  'idx_tikets_user_status');
            $table->index(['status', 'prioritas'],'idx_tikets_status_prioritas');
            $table->index(['created_at'],         'idx_tikets_created_at');
        });

        // ── jurnal_mengajar ────────────────────────────────────────
        // Query: WHERE guru_id + DATE(tanggal) (dashboard guru)
        // Query: WHERE guru_id + semester_id (count semester)
        Schema::table('jurnal_mengajar', function (Blueprint $table) {
            $table->index(['guru_id', 'tanggal'],    'idx_jurnal_guru_tgl');
            $table->index(['guru_id', 'semester_id'],'idx_jurnal_guru_sem');
            $table->index(['tanggal'],               'idx_jurnal_tgl');
        });
    }

    public function down(): void
    {
        Schema::table('absensi_siswa',    fn($t) => $t->dropIndex('idx_absensi_siswa_siswa_status'));
        Schema::table('absensi_harian',   function($t) {
            $t->dropIndex('idx_absensi_harian_tgl_sem');
            $t->dropIndex('idx_absensi_harian_guru_tgl');
        });
        Schema::table('pelanggaran_siswa', function($t) {
            $t->dropIndex('idx_pel_siswa_sem_tgl');
            $t->dropIndex('idx_pel_siswa_siswa_sem');
        });
        Schema::table('prestasi', function($t) {
            $t->dropIndex('idx_prestasi_sem_status');
            $t->dropIndex('idx_prestasi_sem_status_tingkat');
            $t->dropIndex('idx_prestasi_dibuat_sem');
        });
        Schema::table('tikets', function($t) {
            $t->dropIndex('idx_tikets_status');
            $t->dropIndex('idx_tikets_user_status');
            $t->dropIndex('idx_tikets_status_prioritas');
            $t->dropIndex('idx_tikets_created_at');
        });
        Schema::table('jurnal_mengajar', function($t) {
            $t->dropIndex('idx_jurnal_guru_tgl');
            $t->dropIndex('idx_jurnal_guru_sem');
            $t->dropIndex('idx_jurnal_tgl');
        });
    }
};
