<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyPlantillaFieldToMediumblobInCadenaPlantillas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cadena_plantillas', function (Blueprint $table) {
        });

        DB::statement("ALTER TABLE cadena_plantillas MODIFY plantilla MEDIUMBLOB");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cadena_plantillas', function (Blueprint $table) {
        });
        DB::statement("ALTER TABLE cadena_plantillas MODIFY plantilla BLOB");
    }
}
