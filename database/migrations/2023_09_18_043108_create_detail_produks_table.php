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
        Schema::create('detail_produk', function (Blueprint $table) {
            $table->id();
            $table->string('kode_produk')->unique();
            $table->foreign('kode_produk')->references('kode_produk')->on('produk');
            $table->string('file_name')->nullable();
            $table->decimal('diskon_persen', 3, 2)->default(0.00);
            $table->decimal('diskon_harga', 18, 2)->default(0.00);
            $table->integer('status_iklan')->default(0);
            $table->dateTime('tgl_upload');
            $table->dateTime('tgl_hapus');
            $table->string('ket')->nullable();
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
        Schema::dropIfExists('detail_produk');
    }
};
