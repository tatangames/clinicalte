<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     *
 */
    public function up(): void
    {
        Schema::create('salida_receta', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_recetas')->unsigned()->nullable();
            $table->bigInteger('id_usuario')->unsigned();
            $table->dateTime('fecha');
            $table->text('notas')->nullable();

            $table->enum('tipo_salida', ['receta', 'manual'])->default('receta');

            $table->foreign('id_recetas')->references('id')->on('recetas');
            $table->foreign('id_usuario')->references('id')->on('usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salida_receta');
    }
};
