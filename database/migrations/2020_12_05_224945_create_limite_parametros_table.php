<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLimiteParametrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('limite_parametros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_limite');
            $table->foreignId('id_parametro');
            $table->decimal('maximo', $precision = 15, $scale = 7)->nullable();
            $table->decimal('minimo', $precision = 15, $scale = 7)->nullable();
            $table->timestamps();
            $table->index(['id_limite', 'id_parametro']);
            $table->foreign('id_limite')->references('id')->on('limites');
            $table->foreign('id_parametro')->references('id')->on('parametros');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('limite_parametros');
    }
}
