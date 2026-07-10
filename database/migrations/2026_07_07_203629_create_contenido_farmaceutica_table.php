<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TIPO FARMACEUTICA
     *
     * 1- ENVASE
     * 2- FORMA FARMACEUTICA
     * 3- CONCENTRACION
     * 4- CONTENIDO
     * 5- VIA ADMINISTRACION
     *
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('contenido_farmaceutica', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_tipo_farmaceutica')->unsigned();
            $table->string('nombre', 300);

            $table->foreign('id_tipo_farmaceutica')->references('id')->on('tipo_farmaceutica');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contenido_farmaceutica');
    }
};
