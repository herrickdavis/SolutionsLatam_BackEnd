<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificados', function (Blueprint $table) {
            $table->id();
            $table->string('id_certificado', 15)->unique();
            $table->string('nombre_documento');
            $table->string('ruta');
            $table->string('extension', 10);
            $table->foreignId('id_tipo_documento');
            $table->string('identificacion_certificado', 15);
            $table->string('titulo_certificado', 250)->nullable();
            $table->enum('activo', ['S','N']);
            $table->timestamps();
            $table->index('id_certificado');
            $table->index('id_tipo_documento');
            $table->foreign('id_tipo_documento')->references('id')->on('tipo_documentos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificados');
    }
}
