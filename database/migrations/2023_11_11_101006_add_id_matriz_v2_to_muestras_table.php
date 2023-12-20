<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdMatrizV2ToMuestrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('muestras', function (Blueprint $table) {
            $table->unsignedBigInteger('id_matriz_v2')->after('id_matriz')->nullable();
            $table->foreign('id_matriz_v2')->references('id')->on('matrices_v2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('muestras', function (Blueprint $table) {
            $table->dropForeign(['id_matriz_v2']);
            $table->dropColumn('id_matriz_v2');
        });
    }
}
