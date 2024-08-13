<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MainBrand extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('main_brand', function (Blueprint $table) {
            $table->id(); // Chave primária
            $table->string('name');
            $table->string('follow_tags')->nullable();
            $table->string('mentions')->nullable();
            $table->timestamp('past_stamp')->nullable();

            // Chave estrangeira para Account
            $table->foreignId('account_id')->constrained()->onDelete('cascade');

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
