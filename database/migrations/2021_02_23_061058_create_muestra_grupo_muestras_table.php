<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMuestraGrupoMuestrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('muestra_grupo_muestras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_grupo_muestra');
            $table->foreignId('id_muestra');
            $table->index('id_grupo_muestra');
            $table->index('id_muestra');
            $table->foreign('id_grupo_muestra')->references('id')->on('grupo_muestras');
            $table->foreign('id_muestra')->references('id')->on('muestras');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('muestra_grupo_muestras');
    }
}
