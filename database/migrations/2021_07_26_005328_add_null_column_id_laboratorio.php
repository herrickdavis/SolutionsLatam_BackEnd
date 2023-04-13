<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNullColumnIdLaboratorio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('muestra_metodos', function (Blueprint $table) {
            $table->foreignId('id_laboratorio')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('muestra_metodos', function (Blueprint $table) {
            $table->foreignId('id_laboratorio')->nullable(false)->change();
        });
    }
}
