<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cotizaciones_solferino', function (Blueprint $table) {
            $table->id('cotizacion_id');
            $table->unsignedBigInteger('proyecto_id');
            
            // Totales
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('envio', 10, 2)->default(0);
            $table->decimal('instalacion', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('iva', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('saldo_afavor', 10, 2)->default(0);
            
            // Campos específicos para Solferino
            $table->string('tiempo_entrega')->nullable();
            $table->text('observaciones')->nullable();
            
            // Control
            $table->boolean('autorizado')->default(0);
            
            $table->timestamps();

            // Opcional: Agregar llave foránea si tienes la restricción estricta
            // $table->foreign('proyecto_id')->references('proyecto_id')->on('Proyectos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cotizaciones_solferino');
    }
};
