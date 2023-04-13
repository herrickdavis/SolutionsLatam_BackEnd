<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->foreignId('id_empresa');
            $table->foreignId('ver_como')->nullable();
            $table->foreignId('id_rol');
            $table->enum('ver_empresa_sol', ['S','N'])->default('S')->nullable();
            $table->enum('ver_contacto_sol', ['S','N'])->default('S')->nullable();
            $table->enum('ver_empresa_con', ['S','N'])->nullable();
            $table->enum('ver_contacto_con', ['S','N'])->nullable();
            $table->string('idioma', 5)->default('es');
            $table->rememberToken();
            $table->enum('data_campo', ['S','N'])->default('N');
            $table->enum('activo', ['S','N'])->default('S');
            $table->timestamps();
            $table->index('id_empresa');
            $table->index('id_rol');
            $table->foreign('id_empresa')->references('id')->on('empresas');
            $table->foreign('id_rol')->references('id')->on('rols');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
