<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoMuestraGrupoTipoMuestrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_muestra_grupo_tipo_muestras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_grupo_tipo_muestra');
            $table->foreignId('id_tipo_muestra');
            $table->enum('activo', ['S','N']);
            $table->timestamps();
            $table->index('id_grupo_tipo_muestra');
            $table->index('id_tipo_muestra');
            $table->foreign('id_grupo_tipo_muestra')->references('id')->on('grupo_tipo_muestras');
            $table->foreign('id_tipo_muestra')->references('id')->on('tipo_muestras');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_muestra_grupo_tipo_muestras');
    }
}
