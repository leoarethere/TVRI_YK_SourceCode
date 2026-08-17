<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_acaras', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->time('start_time');

            // SEBELUM:
            // $table->foreignId('broadcast_category_id')->constrained('broadcast_categories')->onDelete('cascade');

            // SESUDAH:
            $table->foreignId('broadcast_category_id')
                ->constrained('broadcast_categories')
                ->onDelete('restrict'); // Ubah ke restrict

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_acaras');
    }
};
