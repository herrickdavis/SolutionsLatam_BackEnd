<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCadenaLaboratorioParametrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cadena_laboratorio_parametros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_cadena');
            $table->string('parametro',100);
            $table->string('valor',100);
            $table->string('unidad',50);
            $table->timestamps();
            $table->index('id_cadena');
            $table->foreign('id_cadena')->references('id')->on('cadenas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cadena_laboratorio_parametros');
    }
}
