<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_produk', function (Blueprint $table) {
            $table->id();
            $table->string('uuid_user');
            $table->foreign('uuid_user')->references('uuid')->on('users');
            $table->string('kode_produk');
            $table->foreign('kode_produk')->references('kode_produk')->on('produk');
            $table->bigInteger('id_form');
            $table->foreign('id_form')->references('id')->on('list_form_produk');
            $table->longText('value');
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
        Schema::dropIfExists('form_produk');
    }
};
