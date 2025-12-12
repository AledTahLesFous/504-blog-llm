<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
Schema::create('debunks', function (Blueprint $table) {
    $table->id();
    $table->string('article_id')->unique(); // MD5 = 32 caractères
    $table->string('title');
    $table->string('subtitle')->nullable();
    $table->text('content');
    $table->timestamps();

    $table->foreign('article_id')
          ->references('id')
          ->on('articles')
          ->onDelete('cascade'); // supprime le debunk si l'article est supprimé
});

    }

    public function down(): void
    {
        Schema::dropIfExists('debunks');
    }
};
