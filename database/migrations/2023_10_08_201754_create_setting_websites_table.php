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
        Schema::create('setting_website', function (Blueprint $table) {
            $table->id();
            $table->string('app_name')->default('IORSEL.COM');
            $table->integer('lama_clearing_saldo')->default(3);
            $table->decimal('biaya_platform', 18)->default(10);
            $table->decimal('biaya_admin', 18)->default(0);
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
        Schema::dropIfExists('setting_website');
    }
};
