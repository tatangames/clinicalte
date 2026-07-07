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
        Schema::create('paciente', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_tipo')->unsigned();
            $table->bigInteger('id_estado_civil')->unsigned();
            $table->bigInteger('id_tipo_documento')->unsigned();
            $table->bigInteger('id_profesion')->unsigned();

            $table->string('nombres', 150)->nullable();
            $table->string('apellidos', 150)->nullable();
            $table->date('fecha_nacimiento');
            $table->char('sexo')->nullable();
            $table->string('referido_por', 300)->nullable();
            $table->string('num_documento', 100)->nullable();
            $table->string('correo', 150)->nullable();
            $table->string('celular', 25)->nullable();
            $table->string('telefono', 25)->nullable();
            $table->string('direccion', 550)->nullable();
            $table->string('foto', 100)->nullable();
            $table->string('numero_expediente', 100);

            $table->foreign('id_tipo_documento')->references('id')->on('tipo_documento');
            $table->foreign('id_tipo')->references('id')->on('tipo_paciente');
            $table->foreign('id_estado_civil')->references('id')->on('estado_civil');
            $table->foreign('id_profesion')->references('id')->on('profesion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paciente');
    }
};
