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
        Schema::create('saldo_clearings', function (Blueprint $table) {
            $table->id();
            $table->string('no_order');
            $table->foreign('no_order')->references('no_order')->on('orders');
            $table->string('kode_trx');
            $table->foreign('kode_trx')->references('kode_trx')->on('transaksi');
            $table->string('kode_toko');
            $table->foreign('kode_toko')->references('kode_toko')->on('detail_toko');
            $table->string('status_clearing');
            $table->decimal('total_saldo', 18, 2)->default(0.00);
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
        Schema::dropIfExists('saldo_clearings');
    }
};
