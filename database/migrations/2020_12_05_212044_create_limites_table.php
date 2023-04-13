<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLimitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('limites', function (Blueprint $table) {
            $table->id();
            $table->string("nombre_limite", 250);
            $table->string("desc_limite", 250)->nullable();
            $table->enum("de_empresa", ['S','N'])->default('N');
            $table->enum("activo", ['S','N']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('limites');
    }
}
