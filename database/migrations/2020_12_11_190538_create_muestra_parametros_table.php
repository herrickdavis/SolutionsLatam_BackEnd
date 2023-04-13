<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMuestraParametrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('muestra_parametros', function (Blueprint $table) {
            $table->id();
            $table->foreignId("id_muestra");
            $table->foreignId("id_parametro");
            $table->foreignId("id_metodo")->nullable();
            $table->string("valor")->nullable();
            $table->foreignId("id_unidad")->nullable();
            $table->foreignId("id_parecer");
            $table->timestamps();
            $table->unique(['id_muestra','id_parametro']);
            $table->index('id_muestra');
            $table->index('id_parametro');
            $table->index('id_metodo');
            $table->index('id_unidad');
            $table->index('id_parecer');
            $table->foreign('id_muestra')->references('id')->on('muestras');
            $table->foreign('id_parametro')->references('id')->on('parametros');
            $table->foreign('id_metodo')->references('id')->on('metodos');
            $table->foreign('id_unidad')->references('id')->on('unidades');
            $table->foreign('id_parecer')->references('id')->on('parecer_parametros');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('muestra_parametros');
    }
}
