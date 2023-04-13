<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMuestraMetodosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('muestra_metodos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_muestra');
            $table->foreignId('id_metodo');
            $table->foreignId('id_laboratorio');
            $table->timestamps();
            $table->index('id_muestra');
            $table->index('id_metodo');
            $table->index('id_laboratorio');
            $table->unique(['id_muestra','id_metodo']);
            $table->foreign('id_muestra')->references('id')->on('muestras');
            $table->foreign('id_metodo')->references('id')->on('metodos');
            $table->foreign('id_laboratorio')->references('id')->on('laboratorios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('muestra_metodos');
    }
}
