<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user');
            $table->ipAddress('ip');
            $table->string('url', 100);
            $table->integer('id_muestra')->nullable();
            $table->integer('id_grupo')->nullable();
            $table->string('hash', 32);
            $table->json('payload');
            $table->text('exception')->nullable();
            $table->dateTime('created_at', $precision = 0);
            $table->index('id_user');
            //$table->foreign('id_user')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_uploads');
    }
}
