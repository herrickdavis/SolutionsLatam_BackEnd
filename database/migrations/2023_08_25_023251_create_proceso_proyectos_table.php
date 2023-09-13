<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcesoProyectosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proceso_proyectos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_proceso');
            $table->string("nombre_proyecto");
            $table->string("alias_proyecto")->nullable();
            $table->timestamps();
            $table->index('id_proceso');
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
        Schema::dropIfExists('proceso_proyectos');
    }
}
