<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Delta extends Migration
{
    /**
     * Execute a migração.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delta', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('week');
            $table->unsignedInteger('year');
            $table->foreignId('main_brand_id')->constrained('main_brands')->onDelete('cascade');
            $table->json('primary_posts')->nullable();  // Armazena um array de strings
            $table->json('opponents_posts')->nullable();  // Armazena um array de strings
            $table->timestamps();
        });
    }

    /**
     * Reverte a migração.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delta');
    }
}
