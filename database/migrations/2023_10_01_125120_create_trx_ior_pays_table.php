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
        Schema::create('trx_ior_pays', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pay');
            $table->foreign('kode_pay')->references('kode_pay')->on('ior_pays');
            $table->string('uuid_user');
            $table->foreign('uuid_user')->references('uuid')->on('users');
            $table->string('keterangan');
            $table->string('type_pay');
            $table->decimal('total_trx', 18)->default(0);
            $table->integer('kode_unique')->nullable();
            $table->decimal('biaya_adm')->default(0);
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
        Schema::dropIfExists('trx_ior_pays');
    }
};
