<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Account extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account', function (Blueprint $table) {
            $table->id(); // Chave primária
            $table->string('name');
            $table->string('token');
            $table->string('payment_method');
            $table->integer('installments');
            $table->integer('contract_time');
            $table->boolean('paid')->default(false);
            $table->string('contract_type');
            $table->text('contract_description');
            $table->integer('contract_brands');
            $table->integer('contract_brand_opponents');
            $table->integer('contract_users');
            $table->integer('contract_build_brand_time');
            $table->integer('contract_monitored');
            $table->integer('cancel_time');
            $table->boolean('active')->default(true);

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
        Schema::dropIfExists('account');
    }
}
