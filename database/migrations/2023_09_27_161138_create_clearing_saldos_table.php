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
        Schema::create('clearing_saldo', function (Blueprint $table) {
            $table->id();
            $table->string('kode_toko');
            $table->foreign('kode_toko')->references('kode_toko')->on('detail_toko');
            $table->decimal(18, 2)->default(0);
            $table->date('tanggal');
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
        Schema::dropIfExists('clearing_saldo');
    }
};
