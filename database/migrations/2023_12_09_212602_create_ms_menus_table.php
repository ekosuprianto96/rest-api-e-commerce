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
        Schema::create('ms_menus', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nama_alias')->nullable();
            $table->string('url');
            $table->string('icon');
            $table->string('id_parent');
            $table->integer('an')->default(1);
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
        Schema::dropIfExists('ms_menus');
    }
};
