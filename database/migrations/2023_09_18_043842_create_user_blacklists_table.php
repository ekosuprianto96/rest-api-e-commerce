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
        Schema::create('user_blacklists', function (Blueprint $table) {
            $table->id();
            $table->string('uuid_user')->unique();
            $table->foreign('uuid_user')->references('uuid')->on('users');
            $table->string('keterangan');
            $table->string('blacklist_by');
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
        Schema::dropIfExists('user_blacklists');
    }
};
