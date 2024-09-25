<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Lead extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead', function (Blueprint $table) {
            $table->id(); // Chave primária
            $table->string('name')->nullable();
            $table->json('platform_ids')->nullable();
            $table->json('shortcodes')->nullable();
            $table->boolean('status')->default(true);
            $table->float('score')->default(0.2);
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->foreignId('main_brand_id')->constrained('main_brand')->onDelete('cascade');
            $table->timestamps(); // Cria os campos created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('main_brand');
    }
}
