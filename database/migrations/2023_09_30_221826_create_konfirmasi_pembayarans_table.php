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
        Schema::create('konfirmasi_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->string('no_order');
            $table->foreign('no_order')->references('no_order')->on('orders');
            $table->string('kode_payment');
            $table->foreign('kode_payment')->references('kode_payment')->on('payment_methods');
            $table->string('method');
            $table->decimal('total_biaya', 18, 2)->default(0);
            $table->decimal('total_potongan', 18, 2)->default(0);
            $table->decimal('biaya_platform', 18, 2)->default(0);
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
        Schema::dropIfExists('konfirmasi_pembayaran');
    }
};
