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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_trx')->unique();
            $table->string('uuid_user');
            $table->foreign('uuid_user')->references('uuid')->on('users');
            $table->string('kode_produk');
            $table->foreign('kode_produk')->references('kode_produk')->on('produk');
            $table->string('nm_produk');
            $table->string('kategori');
            $table->string('nama_member');
            $table->string('total_transaksi');
            $table->string('method_payment');
            $table->dateTime('tgl_trx');
            $table->dateTime('tgl_batal');
            $table->integer('status_referal')->default(0);
            $table->string('status_trx');
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
        Schema::dropIfExists('transaksi');
    }
};
