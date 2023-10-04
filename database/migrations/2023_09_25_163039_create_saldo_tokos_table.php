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
        Schema::create('saldo_toko', function (Blueprint $table) {
            $table->id();
            $table->string('kode_toko');
            $table->foreign('kode_toko')->references('kode_toko')->on('detail_toko');
            $table->string('kode_payment');
            $table->decimal('total_saldo', 18, 2)->default(0);
            $table->integer('status_payment');
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
        Schema::dropIfExists('saldo_toko');
    }
};
