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
        Schema::create('farmacia_articulo', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_linea')->unsigned();
            $table->bigInteger('id_sublinea')->unsigned()->nullable();

            $table->string('nombre', 300);
            $table->string('codigo_articulo', 300)->nullable();

            $table->integer('existencia_minima')->nullable();

            $table->foreign('id_linea')->references('id')->on('linea');
            $table->foreign('id_sublinea')->references('id')->on('sub_linea');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farmacia_articulo');
    }
};
