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
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('kode_produk')->unique();
            $table->string('kode_toko');
            $table->foreign('kode_toko')->references('kode_toko')->on('detail_toko');
            $table->string('kode_kategori');
            $table->foreign('kode_kategori')->references('kode_kategori')->on('kategori');
            $table->string('nm_produk');
            $table->string('slug')->unique();
            $table->string('type_produk');
            $table->longText('deskripsi');
            $table->decimal('harga', 18, 2)->default(0.00);
            $table->string('link_referal');
            $table->string('image');
            $table->integer('an')->default(1);
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
        Schema::dropIfExists('produk');
    }
};
