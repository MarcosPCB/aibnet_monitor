<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Post extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post', function (Blueprint $table) {
            $table->id();
            $table->string('url')->nullable();
            $table->string('platform_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('tags')->nullable();
            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('shares')->default(0);
            $table->unsignedInteger('reactions_positive')->default(0);
            $table->unsignedInteger('reactions_negative')->default(0);
            $table->unsignedInteger('reactions_neutral')->default(0);
            $table->string('item_url')->nullable();
            $table->boolean('is_video')->default(false);
            $table->boolean('is_image')->default(false);
            $table->boolean('is_external')->default(false);
            $table->string('mentions')->nullable();
            $table->foreignId('internal_platform_id')->constrained('platform')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post');
    }
}
