<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelemetriaParametroGrupoParametrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telemetria_parametro_grupo_parametros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_parametro_id');
            $table->foreignId('parametro_id');
            $table->timestamps();
            $table->index('grupo_parametro_id');
            $table->foreign('grupo_parametro_id')->references('id')->on('telemetria_grupo_parametros');
            $table->index('parametro_id');
            $table->foreign('parametro_id')->references('id')->on('telemetria_parametros');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('telemetria_parametro_grupo_parametros', function (Blueprint $table) {
            $table->dropForeign(['grupo_parametro_id']); // Usa el nombre de la columna
            $table->dropForeign(['parametro_id']); // Usa el nombre de la columna
        });
        Schema::dropIfExists('telemetria_parametro_grupo_parametros');
    }
}
