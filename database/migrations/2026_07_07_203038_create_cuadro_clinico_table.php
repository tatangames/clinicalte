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
        Schema::create('cuadro_clinico', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_consulta')->unsigned();
            $table->bigInteger('id_diagnostico')->unsigned();
            $table->bigInteger('id_usuario')->unsigned();
            $table->text('descripcion');

            $table->foreign('id_consulta')->references('id')->on('consulta_paciente');
            $table->foreign('id_diagnostico')->references('id')->on('diagnosticos');
            $table->foreign('id_usuario')->references('id')->on('usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuadro_clinico');
    }
};
