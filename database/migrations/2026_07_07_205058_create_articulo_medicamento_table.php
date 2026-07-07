<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articulo_medicamento', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_farmacia_articulo')->unsigned();

            // CONTENDIO FARMACEUTICA

            // envase
            // forma farmaceutica
            // concentracion
            // contenido
            // via administracion
            $table->bigInteger('id_con_far_envase')->unsigned()->nullable();
            $table->bigInteger('id_con_far_forma')->unsigned()->nullable();
            $table->bigInteger('id_con_far_concentracion')->unsigned()->nullable();
            $table->bigInteger('id_con_far_contenido')->unsigned()->nullable();
            $table->bigInteger('id_con_far_administra')->unsigned()->nullable();

            $table->string('nombre_generico', 300)->nullable();

            $table->foreign('id_con_far_envase')->references('id')->on('contenido_farmaceutica');
            $table->foreign('id_con_far_forma')->references('id')->on('contenido_farmaceutica');
            $table->foreign('id_con_far_concentracion')->references('id')->on('contenido_farmaceutica');
            $table->foreign('id_con_far_contenido')->references('id')->on('contenido_farmaceutica');
            $table->foreign('id_con_far_administra')->references('id')->on('contenido_farmaceutica');
            $table->foreign('id_farmacia_articulo')->references('id')->on('farmacia_articulo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articulo_medicamento');
    }
};
