<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcesoMuestrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proceso_muestras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_proceso');
            $table->foreignId('id_muestra');
            $table->enum('activo', ['S','N'])->default('S');
            $table->index('id_proceso');
            $table->index('id_muestra');
            $table->foreign('id_muestra')->references('id')->on('muestras');
            $table->foreign('id_proceso')->references('id')->on('procesos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proceso_muestras');
    }
}
