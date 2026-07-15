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
        Schema::create('recetas_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_recetas')->unsigned();
            $table->bigInteger('id_entrada_detalle')->unsigned();
            $table->bigInteger('id_via')->unsigned();

            $table->integer('cantidad'); // es lo que se retira
            $table->text('descripcion')->nullable();

            $table->foreign('id_via')->references('id')->on('via_receta');
            $table->foreign('id_recetas')->references('id')->on('recetas');
            $table->foreign('id_entrada_detalle')->references('id')->on('entrada_medicamento_detalle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recetas_detalle');
    }
};
