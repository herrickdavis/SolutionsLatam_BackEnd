<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string("nombre_empresa", 250);
            $table->string('codigo_empresa', 20);
            $table->enum('data_campo', ['S','N'])->default('N');
            $table->enum('con_historico', ['S','N'])->default('N');
            $table->foreignId('id_pais');
            $table->timestamps();
            $table->index('id_pais');
            $table->foreign('id_pais')->references('id')->on('paises');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('empresas');
    }
}
