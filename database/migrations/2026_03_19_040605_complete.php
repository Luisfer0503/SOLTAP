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
        Schema::create('categorias_fallas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        Schema::create('subcategoria_fallas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('categoria_falla_id')->nullable();
            $table->string('nombre');
            $table->timestamps();
            
            $table->foreign('categoria_falla_id')->references('id')->on('categorias_fallas')->onDelete('cascade');
        });

        Schema::create('fallas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proyecto_id');
            $table->unsignedBigInteger('articulo_id');
            $table->date('fecha');
            $table->string('sem', 10)->nullable();
            $table->string('mes', 20)->nullable();
            $table->integer('cantidad')->default(1);
            $table->unsignedBigInteger('categoria_id')->nullable();
            $table->unsignedBigInteger('subcategoria_id')->nullable();
            $table->text('descripcion')->nullable();
            $table->integer('hh_minutos')->default(0);
            $table->decimal('costo_hh', 10, 2)->default(0);
            $table->decimal('costo_materiales', 10, 2)->default(0);
            $table->decimal('costo_total', 10, 2)->default(0);
            $table->json('origino')->nullable(); // Guardará arreglo de IDs de usuarios
            $table->json('resolvio')->nullable(); // Guardará arreglo de IDs de usuarios
            $table->json('materiales')->nullable(); // Guardará arreglo de objetos (material, costo)
            $table->string('reporte_imagen')->nullable();
            $table->unsignedBigInteger('registrado_por')->nullable();
            $table->timestamps();

            // Llaves foráneas
            $table->foreign('proyecto_id')->references('proyecto_id')->on('Proyectos')->onDelete('cascade');
            $table->foreign('articulo_id')->references('id')->on('proyecto_articulos')->onDelete('cascade');
            $table->foreign('categoria_id')->references('id')->on('categorias_fallas')->onDelete('set null');
            $table->foreign('subcategoria_id')->references('id')->on('subcategoria_fallas')->onDelete('set null');
            $table->foreign('registrado_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fallas');
        Schema::dropIfExists('subcategoria_fallas');
        Schema::dropIfExists('categorias_fallas');
    }
};
