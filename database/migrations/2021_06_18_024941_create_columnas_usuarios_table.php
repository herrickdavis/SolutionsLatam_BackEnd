<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColumnasUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('columnas_usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user');
            $table->tinyInteger('numero_tabla');
            $table->json('orden');
            $table->timestamps();
            $table->index('id_user');
            $table->foreign('id_user')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('columnas_usuarios');
    }
}
