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
        Schema::create('trx_withdraw_ior_pay', function (Blueprint $table) {
            $table->id();
            $table->string('no_trx')->unique();
            $table->string('kode_pay');
            $table->foreign('kode_pay')->references('kode_pay')->on('ior_pays');
            $table->decimal('total_withdraw', 18);
            $table->decimal('biaya_admin', 18);
            $table->decimal('biaya_transfer', 18);
            $table->string('norek_tujuan');
            $table->string('bank_tujuan');
            $table->string('keterangan')->nullable();
            $table->integer('status_withdraw')->default(0);
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
        Schema::dropIfExists('trx_withdraw_ior_pay');
    }
};
