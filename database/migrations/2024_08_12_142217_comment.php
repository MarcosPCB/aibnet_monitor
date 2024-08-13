<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Comment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('platform_id');
            $table->text('message')->nullable();
            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('shares')->default(0);
            $table->string('mentions')->nullable();
            $table->unsignedInteger('reactions_positive')->default(0);
            $table->unsignedInteger('reactions_negative')->default(0);
            $table->unsignedInteger('reactions_neutral')->default(0);
            $table->string('item_url')->nullable();
            $table->boolean('has_video')->default(false);
            $table->boolean('has_image')->default(false);
            $table->boolean('has_external')->default(false);
            $table->enum('user_gender', ['Male', 'Female'])->nullable();
            $table->unsignedInteger('user_age')->nullable();
            $table->unsignedInteger('num_user_followers')->default(0);
            $table->foreignId('post_id')->constrained('post')->onDelete('cascade');
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
        Schema::dropIfExists('comment');
    }
}
