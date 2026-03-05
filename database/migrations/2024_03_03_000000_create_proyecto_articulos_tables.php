<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Limpieza: Eliminamos tablas anteriores si existen para evitar conflictos
        Schema::dropIfExists('Proyecto_Articuloss'); // Tu tabla anterior
        Schema::dropIfExists('proyecto_articulo_materiales');
        Schema::dropIfExists('proyecto_articulos');

        // 2. Crear tabla principal de artículos de proyecto
        Schema::create('proyecto_articulos', function (Blueprint $table) {
            $table->id(); // ID autoincremental
            $table->unsignedBigInteger('proyecto_id'); // Relación con tu tabla Proyectos
            
            $table->string('articulo_produccion_id')->nullable(); // Tu ID manual (ej. PROD-001)
            $table->unsignedBigInteger('categoria_id')->nullable();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            
            // Dimensiones y Peso
            $table->decimal('alto', 10, 2)->default(0);
            $table->decimal('ancho', 10, 2)->default(0);
            $table->decimal('profundo', 10, 2)->default(0);
            $table->decimal('peso', 10, 2)->default(0);
            $table->decimal('cubicaje', 10, 3)->default(0);
            $table->decimal('precio', 10, 2)->default(0); // Agregamos precio
            
            // Cantidades y Divisiones
            $table->integer('cantidad')->default(1);
            $table->boolean('tiene_division')->default(false);
            $table->integer('piezas_divididas')->default(0);
            
            // Logística
            $table->string('es_planta_baja')->default('si');
            $table->text('condiciones_acceso')->nullable();
            $table->boolean('requiere_instalacion')->default(false);
            $table->boolean('requiere_desemplaye')->default(false);
            
            // Archivos
            $table->string('imagen')->nullable();
            $table->string('pdf_archivo')->nullable();
            
            $table->timestamps();
        });

        // 3. Crear tabla para guardar los materiales (Madera, Tela, etc.) de cada artículo
        Schema::create('proyecto_articulo_materiales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proyecto_articulo_id');
            $table->string('tipo_material'); // Ej: Madera, Tela, Cubierta
            $table->text('descripcion'); // Ej: "Pino, Color Nogal"
            $table->timestamps();

            // Relación: Si borras el artículo, se borran sus materiales
            $table->foreign('proyecto_articulo_id')
                  ->references('id')
                  ->on('proyecto_articulos')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyecto_articulo_materiales');
        Schema::dropIfExists('proyecto_articulos');
    }
};
