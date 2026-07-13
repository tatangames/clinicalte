<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntradaMedicamentoDetalleTable extends Migration
{
    /**
     * REGISTRO DE MEDICAMENTO NUEVO - DETALLE
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entrada_medicamento_detalle', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_entrada_medicamento')->unsigned();
            $table->bigInteger('id_medicamento')->unsigned();

            // NOMBRE DE SEGURIDAD DEL MEDICAMENTO
            $table->string('nombre_copia', 300);

            // ESTO SE IRA RESTANDO DE UNA SALIDA
            $table->integer('cantidad');

            // ESTO SERA FIJO PARA REPORTES
            $table->integer('cantidad_fija');

            $table->decimal('precio', 16, 10);
            $table->string('lote', 100);
            $table->date('fecha_vencimiento');

            // COSTO DONACION
            $table->decimal('precio_donacion', 16,10);

            $table->foreign('id_entrada_medicamento')->references('id')->on('entrada_medicamento');
            $table->foreign('id_medicamento')->references('id')->on('farmacia_articulo');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entrada_medicamento_detalle');
    }
}
