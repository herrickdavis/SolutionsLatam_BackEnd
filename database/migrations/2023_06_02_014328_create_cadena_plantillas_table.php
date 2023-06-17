<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCadenaPlantillasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cadena_plantillas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_plantilla',200);
            $table->binary('plantilla');
            $table->string('extension',5);
            $table->enum('activo', ['S', 'N']);
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
        Schema::dropIfExists('cadena_plantillas');
    }
}
