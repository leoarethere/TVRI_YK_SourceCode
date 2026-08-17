<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppids', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');    // Untuk konten Trix Editor
            $table->string('source_link');  // Link Google Drive / Website
            $table->string('cover_image')->nullable(); // Foto Cover
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppids');
    }
};
