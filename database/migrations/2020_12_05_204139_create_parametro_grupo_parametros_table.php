<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParametroGrupoParametrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parametro_grupo_parametros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_grupo_parametro');
            $table->foreignId('id_parametro');
            $table->enum('activo', ['S','N']);
            $table->timestamps();
            $table->index('id_grupo_parametro');
            $table->index('id_parametro');
            $table->foreign('id_grupo_parametro')->references('id')->on('grupo_parametros');
            $table->foreign('id_parametro')->references('id')->on('parametros');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parametro_grupo_parametros');
    }
}
