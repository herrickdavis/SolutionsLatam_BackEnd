<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEddsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('edds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('nombre_reporte',100);
            $table->enum('es_publico', ['S', 'N'])->default('N');
            $table->enum('activo',['S','N'])->default('S');
            $table->timestamps();
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('edds');
    }
}
