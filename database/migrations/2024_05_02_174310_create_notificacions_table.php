<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notificacions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('empresa_id');
            $table->foreignId('tipo_notificacion_id');
            $table->foreignId('nivel_notificacion_id');
            $table->string('titulo',100);
            $table->string('descripcion');
            $table->json('informacion_adicional');
            $table->enum('leido',['S','N']);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('empresa_id')->references('id')->on('empresas');
            $table->foreign('tipo_notificacion_id')->references('id')->on('tipo_notificacions');
            $table->foreign('nivel_notificacion_id')->references('id')->on('nivel_notificacions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notificacions');
    }
}
