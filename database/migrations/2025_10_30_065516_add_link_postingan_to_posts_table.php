<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Menambahkan kolom baru ke tabel 'posts' yang sudah ada
        Schema::table('posts', function (Blueprint $table) {
            $table->string('link_postingan')->nullable()->after('body');
        });
    }

    public function down(): void
    {
        // Logika untuk membatalkan (rollback) migrasi
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('link_postingan');
        });
    }
};
