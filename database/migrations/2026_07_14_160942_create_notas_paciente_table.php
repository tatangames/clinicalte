<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotasPacienteTable extends Migration
{
    /**
     * PARA GUARDAR NOTAS AL USUARIO Y GENERAR REPORTE
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notas_paciente', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_consulta')->unsigned(); // saber que # de consulta fue
            $table->bigInteger('id_paciente')->unsigned(); // obtener las recetas de x paciente

            $table->date('fecha');
            $table->text('nota');

            $table->foreign('id_paciente')->references('id')->on('paciente');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notas_paciente');
    }
}
