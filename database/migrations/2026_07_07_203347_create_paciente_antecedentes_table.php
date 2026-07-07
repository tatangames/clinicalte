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
        Schema::create('paciente_antecedentes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_paciente')->unsigned();
            $table->bigInteger('id_antecedente_medico')->unsigned();

            $table->foreign('id_paciente')->references('id')->on('paciente');
            $table->foreign('id_antecedente_medico')->references('id')->on('antecedentes_medicos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paciente_antecedentes');
    }
};
