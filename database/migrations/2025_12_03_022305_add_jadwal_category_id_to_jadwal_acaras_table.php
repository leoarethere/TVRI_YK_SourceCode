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
            // Menambahkan kolom foreign key (nullable dulu agar tidak error data lama)
            $table->foreignId('jadwal_category_id')->nullable()->constrained('jadwal_categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_acaras', function (Blueprint $table) {
            $table->dropConstrainedForeignId('jadwal_category_id');
        });
    }
};
