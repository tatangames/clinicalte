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
        Schema::create('nota_evolutiva', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_consulta')->unsigned();
            $table->bigInteger('id_diagnostico')->unsigned();
            $table->date('fecha');
            $table->text('nota')->nullable();

            $table->foreign('id_consulta')->references('id')->on('consulta_paciente');
            $table->foreign('id_diagnostico')->references('id')->on('diagnosticos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_evolutiva');
    }
};
