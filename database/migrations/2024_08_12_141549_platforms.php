<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Platforms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platform', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // Tipo da plataforma, como Facebook, Instagram, etc.
            $table->string('url'); // URL do perfil na plataforma
            $table->string('platform_id'); // ID da plataforma, caso seja necessário
            $table->string('platform_id2')->nullable(); // ID da plataforma, caso seja necessário
            $table->string('name'); // Nome da plataforma ou do perfil
            $table->string('avatar_url')->nullable(); // URL do avatar ou imagem de perfil
            $table->text('description')->nullable(); // Descrição do perfil ou da conta
            $table->string('tags')->nullable(); // Tags associadas à conta ou plataforma
            $table->unsignedBigInteger('num_followers')->default(0); // Número de seguidores
            $table->unsignedBigInteger('num_likes')->default(0); // Número de curtidas
            $table->boolean('capture_comments')->default(true); // Captura de comentários
            $table->boolean('capture_users_from_comments')->default(false); // Captura de usuários a partir dos comentários
            $table->boolean('active')->default(true); // Status ativo da plataforma
            $table->foreignId('brand_id')->constrained('brand')->onDelete('cascade'); // Chave estrangeira para a tabela brands
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
        Schema::dropIfExists('platform');
    }
}

