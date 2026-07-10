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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_tipo_proveedor')->unsigned();

            $table->string('nombre', 100);
            $table->string('nombre_comercial', 300)->nullable();

            $table->foreign('id_tipo_proveedor')->references('id')->on('tipo_proveedor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
