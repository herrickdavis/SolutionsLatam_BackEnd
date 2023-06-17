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
            $table->string('estacion',100);
            $table->string('fecha_inicio',100);
            $table->string('hora_inicio',100);
            $table->string('fecha_fin',100);
            $table->string('hora_fin',100);
            $table->unsignedInteger('codigo_laboratorio');
            $table->string('tipo_muestra',100);
            $table->string('coordenada_norte',100);
            $table->string('coordenada_este',100);
            $table->string('zona',100);
            $table->string('altura',100);
            $table->string('cantidad_frascos',100);
            $table->string('observaciones',100);
            $table->string('numero_grupo',100);
            $table->string('numero_proceso',100);
            $table->string('numero_orden_servicio',100);
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
