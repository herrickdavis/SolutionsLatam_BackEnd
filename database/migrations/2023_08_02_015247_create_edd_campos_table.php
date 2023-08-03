<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEddCamposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('edd_campos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_edd');
            $table->string('nombre_tabla',25);
            $table->string('nombre_campo',50);
            $table->string('nombre_mostrar',50)->nullable();
            $table->unsignedTinyInteger('orden_campo');
            $table->enum('activo',['S','N'])->default('S');
            $table->timestamps();
            $table->index('id_edd');
            $table->foreign('id_edd')->references('id')->on('edds');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('edd_campos');
    }
}
