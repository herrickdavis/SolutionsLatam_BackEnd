<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('id_region')->after('id_rol')->nullable();
            $table->index('id_region');
            $table->foreign('id_region')->references('id')->on('regiones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Eliminar la clave foránea
            $table->dropForeign(['id_region']);
            
            // 2. Eliminar el índice creado
            $table->dropIndex(['id_region']);
            
            // 3. Eliminar la columna
            $table->dropColumn('id_region');
        });
    }
}
