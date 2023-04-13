<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataCamposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_campos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_muestra');
            $table->string('numero_grupo', 50)->nullable();
            $table->foreignId('id_empresa_sol');
            $table->foreignId('id_empresa_con');
            $table->string('nombre_proyecto', 100);
            $table->string('nombre_estacion', 100);
            $table->string('tipo_muestra', 100);
            $table->string('info', 100);
            $table->string('valor', 100)->nullable();
            $table->text('observacion')->nullable();
            $table->datetime('fecha_muestreo', 0);
            $table->index('id_muestra');
            $table->index('id_empresa_sol');
            $table->index('id_empresa_con');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_campos');
    }
}
