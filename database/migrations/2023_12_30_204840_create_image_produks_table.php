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
        Schema::create('image_produk', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('kode_produk');
            $table->foreign('kode_produk')->references('kode_produk')->on('produk');
            $table->string('url');
            $table->string('an');
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
        Schema::dropIfExists('image_produk');
    }
};
