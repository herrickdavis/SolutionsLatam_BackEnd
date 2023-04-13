<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProyectoGrupoProyectosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proyecto_grupo_proyectos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_grupo_proyecto');
            $table->foreignId('id_proyecto');
            $table->enum('activo', ['S','N']);
            $table->timestamps();
            $table->index('id_grupo_proyecto');
            $table->index('id_proyecto');
            $table->foreign('id_grupo_proyecto')->references('id')->on('grupo_proyectos');
            $table->foreign('id_proyecto')->references('id')->on('proyectos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proyecto_grupo_proyectos');
    }
}
