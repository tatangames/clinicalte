<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * DATOS GENERALES DE 1 FILA
     */
    public function up(): void
    {
        Schema::create('informacion_general', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informacion_general');
    }
};
