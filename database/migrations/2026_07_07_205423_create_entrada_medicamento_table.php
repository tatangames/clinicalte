<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntradaMedicamentoTable extends Migration
{
    /**
     * REGISTRO DE MEDICAMENTO NUEVO
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entrada_medicamento', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_tipofactura')->unsigned();
            $table->bigInteger('id_fuentefina')->unsigned();
            $table->bigInteger('id_proveedor')->unsigned();
            $table->bigInteger('id_usuario')->unsigned();

            $table->dateTime('fecha');
            $table->string('numero_factura', 100);

            $table->foreign('id_tipofactura')->references('id')->on('tipo_factura');
            $table->foreign('id_fuentefina')->references('id')->on('fuente_financiamiento');
            $table->foreign('id_proveedor')->references('id')->on('proveedores');
            $table->foreign('id_usuario')->references('id')->on('usuario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entrada_medicamento');
    }
}
