<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataExternaTemporalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_externa_temporals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user');
            $table->integer('fila');
            $table->foreignId('id_muestra');
            $table->string('fecha_muestreo',250);            
            $table->foreignId('id_matriz')->nullable();;
            $table->string('matriz',250);
            $table->foreignId('id_tipo_muestra')->nullable();;
            $table->string('tipo_muestra',250);
            $table->foreignId('id_proyecto')->nullable();;
            $table->string('proyecto',250);
            $table->foreignId('id_estacion')->nullable();;
            $table->string('estacion',250);
            $table->foreignId('id_empresa_contratante')->nullable();;
            $table->string('empresa_contratante',250);
            $table->foreignId('id_empresa_solicitante')->nullable();;
            $table->string('empresa_solicitante',250);
            $table->foreignId('id_parametro')->nullable();;
            $table->string('parametro',250);
            $table->string('valor',250);
            $table->foreignId('id_unidad')->nullable();;
            $table->string('unidad',250);            
            $table->timestamps();
            $table->index('id_user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_externa_temporals');
    }
}
