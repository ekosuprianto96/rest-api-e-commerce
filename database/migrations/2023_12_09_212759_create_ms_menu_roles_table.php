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
        Schema::create('ms_menu_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ms_menu_id')->references('id')->on('ms_menus')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreignId('role_id')->references('id')->on('roles')->onDelete('CASCADE')->onUpdate('CASCADE');;
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
        Schema::dropIfExists('ms_menu_roles');
    }
};
