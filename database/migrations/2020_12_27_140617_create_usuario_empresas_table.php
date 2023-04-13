<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuarioEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuario_empresas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario');
            $table->foreignId('id_empresa');
            $table->enum("activo", ['S','N'])->default('S');
            $table->index('id_usuario');
            $table->index('id_empresa');
            $table->foreign('id_usuario')->references('id')->on('users');
            $table->foreign('id_empresa')->references('id')->on('empresas');
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
        Schema::dropIfExists('usuario_empresas');
    }
}
