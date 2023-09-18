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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('release');
            $table->integer('length');
            $table->text('description');
            $table->string('mpaa_rating');
            $table->string('genre_1'); // First genre
            $table->string('genre_2')->nullable(); // Second genre (nullable)
            $table->string('genre_3')->nullable(); // Third genre (nullable)
            $table->string('director');
            $table->string('performer_1'); // First performer
            $table->string('performer_2')->nullable(); // Second performer (nullable)
            $table->string('performer_3')->nullable(); // Third performer (nullable)
            $table->string('language');
            $table->string('poster')->nullable(); // Add the 'poster' column
            $table->decimal('overall_rating', 3, 1)->nullable();
            $table->integer('views')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
