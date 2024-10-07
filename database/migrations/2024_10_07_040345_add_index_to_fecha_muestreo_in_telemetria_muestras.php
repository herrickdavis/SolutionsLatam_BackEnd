<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToFechaMuestreoInTelemetriaMuestras extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telemetria_muestras', function (Blueprint $table) {
            $table->index('fecha_muestreo'); // Añadir índice a la columna 'fecha_muestreo'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('telemetria_muestras', function (Blueprint $table) {
            $table->dropIndex(['fecha_muestreo']); // Elimina el índice si se revierte la migración
        });
    }
}
