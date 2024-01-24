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
        Schema::create('transaksi_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi')->unique();
            $table->string('uuid_user');
            $table->foreign('uuid_user')->references('uuid')->on('users');
            $table->string('type_payment');
            $table->string('method');
            $table->string('jns_payment');
            $table->string('kode_unique')->nullable();
            $table->decimal('biaya_trx', 18);
            $table->decimal('total', 18);
            $table->string('no_refrensi')->nullable();
            $table->string('keterangan')->nullable();
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
        Schema::dropIfExists('transaksi_accounts');
    }
};
