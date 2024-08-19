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
            $table->foreignId('brand_id')->constrained('brand')->onDelete('cascade');
            $table->json('json')->nullable();
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
