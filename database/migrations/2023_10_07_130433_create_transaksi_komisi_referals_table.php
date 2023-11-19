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
        Schema::create('transaksi_komisi_referals', function (Blueprint $table) {
            $table->id();
            $table->string('no_trx');
            $table->foreign('no_trx')->references('no_trx')->on('trx_ior_pays');
            $table->string('kode_produk');
            $table->foreign('kode_produk')->references('kode_produk')->on('produk');
            $table->string('kode_pay');
            $table->foreign('kode_pay')->references('kode_pay')->on('ior_pays');
            $table->decimal('total_komisi', 18);
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
        Schema::dropIfExists('transaksi_komisi_referals');
    }
};
