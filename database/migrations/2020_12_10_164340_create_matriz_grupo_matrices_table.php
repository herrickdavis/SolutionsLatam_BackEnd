<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatrizGrupoMatricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matriz_grupo_matrices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_grupo_matriz');
            $table->foreignId('id_matriz');
            $table->enum('activo', ['S','N']);
            $table->timestamps();
            $table->index('id_grupo_matriz');
            $table->index('id_matriz');
            $table->foreign('id_grupo_matriz')->references('id')->on('grupo_matrices');
            $table->foreign('id_matriz')->references('id')->on('matrices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matriz_grupo_matrices');
    }
}
