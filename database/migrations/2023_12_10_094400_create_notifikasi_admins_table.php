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
        Schema::create('notifikasi_admin', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('type');
            $table->string('target')->nullable();
            $table->json('value');
            $table->integer('status_read');
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
        Schema::dropIfExists('notifikasi_admin');
    }
};
