<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstadoToTableLogUploads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_uploads', function (Blueprint $table) {
            $table->enum('estado', ['P','C'])->default('P')->after('payload');
            $table->index('id_muestra');
            $table->index('id_grupo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('log_uploads', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
}
