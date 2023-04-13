<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLimiteTipoMuestrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('limite_tipo_muestras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_limite');
            $table->foreignId('id_tipo_muestra');
            $table->enum('activo', ['S','N']);
            $table->index(['id_limite','id_tipo_muestra']);
            $table->foreign('id_limite')->references('id')->on('limites');
            $table->foreign('id_tipo_muestra')->references('id')->on('tipo_muestras');
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
        Schema::dropIfExists('limite_tipo_muestras');
    }
}
