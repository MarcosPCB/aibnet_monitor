<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiTokensTable extends Migration
{
    public function up()
    {
        Schema::create('api_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('doc_url');
            $table->string('token');
            $table->string('email');
            $table->integer('limit');
            $table->enum('limit_type', ['daily', 'weekly', 'monthly', 'yearly']);
            $table->dateTime('last_used')->nullable();
            $table->integer('limit_used')->default(0);
            $table->boolean('status')->default(true);
            $table->dateTime('expires')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('api_tokens');
    }
}

