<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('chosen_articles', function (Blueprint $table) {
        $table->string('id')->primary();  // MD5 de l'URL comme clé primaire
        $table->json('data');        // Contient l'article choisi
        $table->string('url')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
public function down()
{
    Schema::dropIfExists('chosen_articles');
}

};
