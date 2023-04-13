<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstacionGrupoEstacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estacion_grupo_estaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_grupo_estacion');
            $table->foreignId('id_estacion');
            $table->enum("activo", ['S','N']);
            $table->timestamps();
            $table->index('id_grupo_estacion');
            $table->index('id_estacion');
            $table->foreign('id_grupo_estacion')->references('id')->on('grupo_estaciones');
            $table->foreign('id_estacion')->references('id')->on('estaciones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estacion_grupo_estaciones');
    }
}
