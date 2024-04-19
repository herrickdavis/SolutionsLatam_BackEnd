<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstadoMuestraToCadenasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cadenas', function (Blueprint $table) {
            $table->string('estado_muestra', 20)->nullable()->after('id_pais');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cadenas', function (Blueprint $table) {
            $table->dropColumn('estado_muestra');
        });
    }
}
