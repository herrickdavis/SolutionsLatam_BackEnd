<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetodosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metodos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idaux_metodo');
            $table->smallInteger('version_metodo');
            $table->string("nombre_metodo", 100);
            $table->string("desc_metodo", 250)->nullable();
            $table->string("referencia_metodo", 250)->nullable();
            $table->enum('es_acreditado', ['S','N']);
            $table->enum('activo', ['S','N']);
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
        Schema::dropIfExists('metodos');
    }
}
