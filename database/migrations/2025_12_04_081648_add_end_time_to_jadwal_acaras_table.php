<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jadwal_acaras', function (Blueprint $table) {
            // Menambahkan kolom end_time setelah start_time
            // Kita buat nullable dulu agar tidak error jika sudah ada data sebelumnya
            $table->time('end_time')->nullable()->after('start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_acaras', function (Blueprint $table) {
            $table->dropColumn('end_time');
        });
    }
};
