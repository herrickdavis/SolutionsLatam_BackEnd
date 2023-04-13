<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empresa_sol');
            $table->foreignId('id_empresa_con');
            $table->string('nombre_estacion');
            $table->decimal('latitud_n', $precision = 15, $scale = 7)->nullable();
            $table->decimal('longitud_e', $precision = 15, $scale = 7)->nullable();
            $table->string('zona', 3)->nullable();
            $table->enum('hemisferio', ['S','N'])->nullable();
            $table->string('procedencia')->nullable();
            $table->enum('activo', ['S','N'])->default('S');
            $table->timestamps();
            $table->index('id_empresa_sol');
            $table->index('id_empresa_con');
            $table->foreign('id_empresa_sol')->references('id')->on('empresas');
            $table->foreign('id_empresa_con')->references('id')->on('empresas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estaciones');
    }
}
