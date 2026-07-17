<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * DETALLE DE LA SALIDA DE MEDICAMENTO DE LA RECETA
 */
    public function up(): void
    {
        Schema::create('salida_receta_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_salidareceta')->unsigned();
            $table->bigInteger('id_entrada_detalle')->unsigned();

            $table->integer('cantidad');

            $table->foreign('id_salidareceta')->references('id')->on('salida_receta');
            $table->foreign('id_entrada_detalle')->references('id')->on('entrada_medicamento_detalle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salida_receta_detalle');
    }
};
