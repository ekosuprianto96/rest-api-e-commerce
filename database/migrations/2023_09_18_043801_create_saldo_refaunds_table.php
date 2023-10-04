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
        Schema::create('saldo_refaund', function (Blueprint $table) {
            $table->id();
            $table->string('uuid_user');
            $table->foreign('uuid_user')->references('uuid')->on('users');
            $table->string('no_order')->nullable();
            $table->foreign('no_order')->references('no_order')->on('orders');
            $table->decimal('total_refaund', 18, 2)->default(0.00);
            $table->decimal('total_potongan')->default(0.00);
            $table->string('ket');
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
        Schema::dropIfExists('saldo_refaund');
    }
};
