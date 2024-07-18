<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelemetriaCriteriosValidacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telemetria_criterios_validacions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id');
            $table->foreignId('tipo_criterio');
            $table->foreignId('tipo_estado');
            $table->string('descripcion',150);
            $table->string('variables',100);
            $table->string('criterio',150);
            $table->string('aplicacion',250);
            $table->enum('activo',['S','N'])->default('S');
            $table->foreign('tipo_criterio')->references('id')->on('telemetria_tipo_criterios_validacions');
            $table->foreign('empresa_id')->references('id')->on('empresas');
            $table->foreign('tipo_estado')->references('id')->on('telemetria_estado_resultados');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('telemetria_criterios_validacions', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
            $table->dropForeign(['tipo_criterio']);
            
        });
        Schema::dropIfExists('telemetria_criterios_validacions');
    }
}
