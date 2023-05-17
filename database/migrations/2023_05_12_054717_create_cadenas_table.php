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
            $table->string('cliente',100);
            $table->string('contacto',100);
            $table->string('correo',100);
            $table->string('lugar_procedencia',100);
            $table->string('proyecto',100);
            $table->string('periodico',100);
            $table->string('numero_grupo',100);
            $table->string('numero_proceso',100);
            $table->string('numero_orden',100);
            $table->string('plan_muestreo',100);
            $table->string('equipos_empleados',100);
            $table->string('firma_responsable_muestreo',100);
            $table->string('nombre_responsable_muestreo',100);
            $table->string('fecha_responsable_muestreo',100);
            $table->string('firma_responsable_transporte',100);
            $table->string('nombre_responsable_transporte',100);
            $table->string('fecha_responsable_transporte',100);
            $table->string('firma_recepcion_muestra',100);
            $table->string('nombre_recepcion_muestra',100);
            $table->string('fecha_recepcion_muestra',100);
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
