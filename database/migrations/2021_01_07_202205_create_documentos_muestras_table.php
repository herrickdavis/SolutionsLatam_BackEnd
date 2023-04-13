<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentosMuestrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentos_muestras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_documento')->nullable();
            $table->foreignId('id_muestra');
            $table->string('nombre_documento');
            $table->string('ruta');
            $table->string('extension', 10);
            $table->foreignId('id_tipo_documento');
            $table->enum('activo', ['S','N']);
            $table->smallInteger('orden')->default(99);
            $table->timestamps();
            $table->index('id_documento');
            $table->index('id_muestra');
            $table->index('id_tipo_documento');
            $table->foreign('id_tipo_documento')->references('id')->on('tipo_documentos');
            $table->foreign('id_muestra')->references('id')->on('muestras');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documentos_muestras');
    }
}
