<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMuestraParametroExternosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('muestra_parametro_externos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_muestra_externa');
            $table->foreignId('id_parametro');
            $table->string("valor")->nullable();
            $table->foreignId("id_unidad")->nullable();
            $table->foreign('id_muestra_externa')->references('id')->on('muestra_externas');
            $table->foreign('id_parametro')->references('id')->on('parametros');
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
        Schema::dropIfExists('muestra_parametro_externos');
    }
}
