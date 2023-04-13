<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresaReportesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresa_reportes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empresa');
            $table->foreignId('id_reporte');
            $table->timestamps();
            $table->index('id_empresa');
            $table->index('id_reporte');
            $table->foreign('id_empresa')->references('id')->on('empresas');
            $table->foreign('id_reporte')->references('id')->on('reportes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('empresa_reportes');
    }
}
