<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('plan_pagos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cotizacion_id');
            $table->string('nombre');
            $table->integer('numero_pago'); // Ej: 1, 2, 3...
            $table->integer('total_pagos_plan'); // Ej: 10 (si se dividió en 10)
            $table->decimal('porcentaje', 5, 2); // Ejemplo: 50.00
            $table->decimal('monto', 12, 2);     // Monto acordado a pagar
            $table->decimal('monto_pagado', 12, 2)->default(0); // Lo que el cliente ya pagó
            $table->string('estatus')->default('pendiente'); // pendiente, pagado, parcial
            $table->date('fecha_pago_real')->nullable(); // Fecha en que se realizó el pago
            $table->timestamps();

            // Clave foránea (Asegúrate que tu tabla 'cotizaciones' tenga la columna 'cotizacion_id' como llave primaria o índice)
            // Si tu tabla cotizaciones usa 'id', cambia 'cotizacion_id' por 'id' en la referencia.
            // $table->foreign('cotizacion_id')->references('cotizacion_id')->on('cotizaciones')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('plan_pagos');
    }
};
