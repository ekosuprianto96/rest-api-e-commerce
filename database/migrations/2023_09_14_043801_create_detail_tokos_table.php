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
        Schema::create('detail_toko', function (Blueprint $table) {
            $table->id();
            $table->string('uuid_user');
            $table->foreign('uuid_user')->references('uuid')->on('users');
            $table->string('kode_toko')->unique();
            $table->string('nama_toko');
            $table->string('alamat_toko');
            $table->longText('deskripsi_toko');
            $table->string('status_toko');
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
        Schema::dropIfExists('detail_toko');
    }
};
