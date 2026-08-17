<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('social_medias', function (Blueprint $table) {
            // Tambahkan kolom phone setelah email
            $table->string('phone')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('social_medias', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }
};
