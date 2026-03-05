<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id('cotizacion_id');
            $table->unsignedBigInteger('proyecto_id');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('envio', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('iva', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizaciones');
    }
};