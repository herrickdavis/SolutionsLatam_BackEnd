<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCadenasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cadenas', function (Blueprint $table) {
            $table->id();            
            $table->string('codigo_laboratorio',10);
            $table->string('numero_grupo',15)->nullable();
            $table->string('numero_proceso',15)->nullable();
            $table->string('numero_orden_servicio',15)->nullable();
            $table->string('estacion',100)->nullable();
            $table->date('fecha_muestreo')->nullable();
            $table->string('tipo_muestra',50)->nullable();
            $table->string('id_empresa',7)->nullable();
            $table->string('id_pais',2)->nullable();
            $table->json('informacion_adicional');
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
        Schema::dropIfExists('cadenas');
    }
}
