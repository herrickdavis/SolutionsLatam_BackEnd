<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCadenaInSituParametrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cadena_in_situ_parametros', function (Blueprint $table) {
            $table->foreignId('id_cadena');
            $table->string('estacion',100);
            $table->string('fecha_inicio',100);
            $table->string('hora_inicio',100);
            $table->string('fecha_fin',100);
            $table->string('hora_fin',100);
            $table->string('codigo_laboratorio',100);
            $table->string('matriz',100);
            $table->string('coordenada_sur',100);
            $table->string('coordenada_oeste',100);
            $table->string('zona',100);
            $table->string('altura',100);
            $table->string('cantidad_frascos',100);
            $table->string('observaciones',100);
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
        Schema::dropIfExists('cadena_in_situ_parametros');
    }
}
