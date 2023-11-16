<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFechaMuestreoIndexToMuestrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('muestras', function (Blueprint $table) {
            $table->index('fecha_muestreo');
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
            $table->dropIndex(['fecha_muestreo']);
        });
    }
}
