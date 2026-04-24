<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class proyecto_articulos extends Model
{
    protected $table = 'proyecto_articulos';
    protected $fillable = ['proyecto_id', 'articulo_produccion_id', 'categoria_id', 'nombre', 'descripcion', 'alto', 'ancho', 'profundo', 'peso', 'cubicaje', 'precio', 'cantidad', 'tiene_division', 'piezas_divididas', 'es_planta_baja', 'condiciones_acceso', 'requiere_instalacion', 'requiere_desemplaye', 'imagen', 'pdf_archivo'];

    public function proyecto()
    {
        return $this->belongsTo(proyectos::class, 'proyecto_id');
    }
}
