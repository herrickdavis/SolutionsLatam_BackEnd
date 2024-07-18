<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelemetriaParametrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telemetria_parametros', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_parametro',50)->unique();
            $table->foreignId('id_tipo_parametro');
            $table->timestamps();
            $table->index('id_tipo_parametro');
            $table->foreign('id_tipo_parametro')->references('id')->on('telemetria_tipo_parametros');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telemetria_parametros');
    }
}
