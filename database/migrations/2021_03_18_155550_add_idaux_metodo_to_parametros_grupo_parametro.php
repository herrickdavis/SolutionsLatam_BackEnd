<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdauxMetodoToParametrosGrupoParametro extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parametro_grupo_parametros', function (Blueprint $table) {
            $table->foreignId('idaux_metodo')->nullable()->after('id_parametro');
            $table->index('idaux_metodo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parametro_grupo_parametros', function (Blueprint $table) {
            $table->dropColumn('idaux_metodo');
        });
    }
}