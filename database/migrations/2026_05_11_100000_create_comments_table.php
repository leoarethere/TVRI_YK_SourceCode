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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();

            // Polymorphic relation columns (commentable_id, commentable_type)
            $table->morphs('commentable');

            // Identitas pengunjung
            $table->string('name');
            $table->string('email');

            // Isi Komentar dan Rating
            $table->text('body');
            $table->unsignedTinyInteger('rating')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
