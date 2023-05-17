<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdEmpresasToProyectosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->foreignId('id_empresa_sol')->nullable()->index();
            $table->foreignId('id_empresa_con')->nullable()->index();
            $table->foreign('id_empresa_sol')->references('id')->on('empresas');
            $table->foreign('id_empresa_con')->references('id')->on('empresas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropColumn('id_empresa_sol');
            $table->dropColumn('id_empresa_con');
        });
    }
}
