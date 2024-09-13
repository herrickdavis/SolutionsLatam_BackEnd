<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnToTelemetriaResultadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telemetria_resultados', function (Blueprint $table) {
            $table->string('direccion_viento', 15)->nullable()->after('estado_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('telemetria_resultados', function (Blueprint $table) {
            $table->dropColumn('direccion_viento');
        });
    }
}
