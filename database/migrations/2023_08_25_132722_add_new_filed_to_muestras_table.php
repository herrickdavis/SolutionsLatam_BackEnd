<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFiledToMuestrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('muestras', function (Blueprint $table) {
            $table->foreignId('id_proceso_proyecto')->after('id_proyecto')->nullable();
            $table->index('id_proceso_proyecto');
            $table->foreign('id_proceso_proyecto')->references('id')->on('proceso_proyectos');
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
            $table->dropForeign(['id_proceso_proyecto']);
            $table->dropIndex(['id_proceso_proyecto']);
            $table->dropColumn('id_proceso_proyecto');
        });
    }
}
