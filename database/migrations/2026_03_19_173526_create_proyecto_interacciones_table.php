<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyecto_interacciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('proyecto_id');
            // Usamos integer por si el ID de interacciones es string o id normal
            $table->string('interaccion_id'); 
            $table->text('comentarios')->nullable();
            $table->timestamps();

            $table->foreign('proyecto_id')->references('proyecto_id')->on('Proyectos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyecto_interacciones');
    }
};
