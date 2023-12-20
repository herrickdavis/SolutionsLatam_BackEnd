<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMuestraExternasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('muestra_externas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_muestra');
            $table->foreignId('id_matriz_v2');
            $table->foreignId('id_tipo_muestra');
            $table->foreignId('id_proyecto');
            $table->foreignId('id_estacion');
            $table->foreignId('id_empresa_sol');
            $table->foreignId('id_empresa_con');
            $table->timestamps();
            $table->foreign('id_matriz_v2')->references('id')->on('matrices_v2');
            $table->foreign('id_tipo_muestra')->references('id')->on('tipo_muestras');
            $table->foreign('id_proyecto')->references('id')->on('proyectos');
            $table->foreign('id_empresa_sol')->references('id')->on('empresas');
            $table->foreign('id_empresa_con')->references('id')->on('empresas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('muestra_externas');
    }
}
