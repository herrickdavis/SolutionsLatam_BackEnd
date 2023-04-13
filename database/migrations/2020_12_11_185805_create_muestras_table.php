<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMuestrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('muestras', function (Blueprint $table) {
            $table->id();
            $table->string("numero_muestra", 20);
            $table->foreignId("id_motivo_muestra");
            $table->foreignId("id_estado");
            $table->foreignId("id_parecer");
            $table->enum("con_data", ['S','N']);
            $table->foreignId("id_empresa_con");
            $table->foreignId("id_user_con");
            $table->foreignId("id_empresa_sol");
            $table->foreignId("id_user_sol");
            $table->foreignId("id_estacion");
            $table->foreignId("id_proyecto");
            $table->foreignId("id_tipo_muestra");
            $table->foreignId("id_matriz");
            $table->foreignId("id_limite")->nullable();
            $table->dateTime('fecha_muestreo', 0);
            $table->dateTime('fecha_prevista_entrega', 0)->nullable();
            $table->dateTime('fecha_publicacion', 0)->nullable();
            $table->enum('con_documentos', ['S','N'])->default('N');
            $table->string('id_certificado', 15)->nullable();
            $table->enum('activo', ['S','N']);
            $table->timestamps();
            $table->index('id_motivo_muestra');
            $table->index('id_estado');
            $table->index('id_parecer');
            $table->index('id_empresa_con');
            $table->index('id_user_con');
            $table->index('id_empresa_sol');
            $table->index('id_user_sol');
            $table->index('id_estacion');
            $table->index('id_proyecto');
            $table->index('id_tipo_muestra');
            $table->index('id_matriz');
            $table->index('id_limite');
            $table->index('id_certificado');
            $table->foreign('id_motivo_muestra')->references('id')->on('motivo_muestras');
            $table->foreign('id_estado')->references('id')->on('estado_muestras');
            $table->foreign('id_parecer')->references('id')->on('parecer_parametros');
            $table->foreign('id_empresa_con')->references('id')->on('empresas');
            $table->foreign('id_user_con')->references('id')->on('users');
            $table->foreign('id_empresa_sol')->references('id')->on('empresas');
            $table->foreign('id_user_sol')->references('id')->on('users');
            $table->foreign('id_estacion')->references('id')->on('estaciones');
            $table->foreign('id_proyecto')->references('id')->on('proyectos');
            $table->foreign('id_tipo_muestra')->references('id')->on('tipo_muestras');
            $table->foreign('id_matriz')->references('id')->on('matrices');
            $table->foreign('id_limite')->references('id')->on('limites');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('muestras');
    }
}
