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
        Schema::create('batal_orders', function (Blueprint $table) {
            $table->id();
            $table->string('no_order');
            $table->foreign('no_order')->references('no_order')->on('orders');
            $table->string('kode_produk');
            $table->foreign('kode_produk')->references('kode_produk')->on('produk');
            $table->string('alasan')->nullable();
            $table->string('payment_method');
            $table->bigInteger('quantity')->default(0);
            $table->decimal('total_biaya', 18, 2)->default(0.00);
            $table->decimal('total_potongan', 18, 2)->default(0.00);
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
        Schema::dropIfExists('batal_orders');
    }
};
