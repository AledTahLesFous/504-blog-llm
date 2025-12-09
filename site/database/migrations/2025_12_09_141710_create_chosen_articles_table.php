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
        $table->id();
        $table->json('data');        // Contient l’article choisi
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
