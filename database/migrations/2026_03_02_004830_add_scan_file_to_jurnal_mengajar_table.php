<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jurnal_mengajar', function (Blueprint $table) {
            $table->string('scan_file')->nullable()->after('tanda_tangan');
        });
    }

    public function down(): void
    {
        Schema::table('jurnal_mengajar', function (Blueprint $table) {
            $table->dropColumn('scan_file');
        });
    }
};
