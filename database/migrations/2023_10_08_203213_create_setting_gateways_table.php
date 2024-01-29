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
        Schema::create('setting_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('server_key')->nullable();
            $table->string('client_key')->nullable();
            $table->integer('is_production')->default(0);
            $table->integer('is_3ds')->default(0);
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
        Schema::dropIfExists('setting_gateways');
    }
};
