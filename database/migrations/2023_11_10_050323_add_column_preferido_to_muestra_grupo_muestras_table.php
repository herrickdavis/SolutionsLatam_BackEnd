<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPreferidoToMuestraGrupoMuestrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('muestra_grupo_muestras', function (Blueprint $table) {
            $table->char('preferido', 1)->default('N')->after('id_muestra');
            $table->index('preferido');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('muestra_grupo_muestras', function (Blueprint $table) {
            $table->dropIndex(['preferido']);
            $table->dropColumn('preferido');
        });
    }
}
