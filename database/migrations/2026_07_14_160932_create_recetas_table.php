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
        Schema::create('recetas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_consulta')->unsigned(); // saber que # de consulta fue
            $table->bigInteger('id_paciente')->unsigned(); // obtener las recetas de x paciente
            $table->bigInteger('id_diagnostico')->unsigned();
            $table->bigInteger('id_usuario')->unsigned();

            $table->text('descripcion_general')->nullable();

            // cuando el doctor creo la receta
            $table->date('fecha');
            $table->date('proxima_cita')->nullable();

            // estado de entrega de receta

            // 1: pendiente
            // 2: procesada
            // 3: denegada

            $table->integer('estado');

            // cuando se modifico el estado, unicamente se ocupara cuando se
            // denego la receta, ya que si se despacha, esto se ocupara la
            // fecha de tabla salida_receta
            $table->dateTime('fecha_estado')->nullable();
            $table->string('nota_denegada', 500)->nullable();
            // usuario quien denego receta
            $table->bigInteger('id_usuario_estado')->unsigned()->nullable();

            $table->foreign('id_consulta')->references('id')->on('consulta_paciente');
            $table->foreign('id_paciente')->references('id')->on('paciente');
            $table->foreign('id_diagnostico')->references('id')->on('diagnosticos');
            $table->foreign('id_usuario')->references('id')->on('usuario');
            $table->foreign('id_usuario_estado')->references('id')->on('usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recetas');
    }
};
