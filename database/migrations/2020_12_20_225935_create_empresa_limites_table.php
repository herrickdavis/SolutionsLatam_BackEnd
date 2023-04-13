<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresaLimitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresa_limites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empresa');
            $table->foreignId('id_limite');
            $table->enum('activo', ['S','N']);
            $table->index('id_empresa');
            $table->index('id_limite');
            $table->foreign('id_empresa')->references('id')->on('empresas');
            $table->foreign('id_limite')->references('id')->on('limites');
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
        Schema::dropIfExists('empresa_limites');
    }
}
