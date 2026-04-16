<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rombels', function (Blueprint $table) {
            $table->string('jenis_rombel')->default('1')->after('kurikulum');
            $table->string('jenis_rombel_str')->nullable()->after('jenis_rombel');
            $table->string('wali_kelas')->nullable()->after('jenis_rombel_str'); // nama PTK/pembina
            $table->string('ptk_id')->nullable()->after('wali_kelas');           // UUID PTK dari Dapodik
        });
    }

    public function down(): void
    {
        Schema::table('rombels', function (Blueprint $table) {
            $table->dropColumn(['jenis_rombel', 'jenis_rombel_str', 'wali_kelas', 'ptk_id']);
        });
    }
};
