<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelemetriaLimiteParametrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telemetria_limite_parametros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('limite_id');
            $table->foreignId('parametro_id');
            $table->string('limite_inferior',20);
            $table->string('limite_superior',20);
            $table->timestamps();
            $table->foreign('limite_id')->references('id')->on('telemetria_limites');
            $table->foreign('parametro_id')->references('id')->on('telemetria_parametros');
            $table->unique(['limite_id', 'parametro_id'], 'limite_id_parametro_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telemetria_limite_parametros');
    }
}
