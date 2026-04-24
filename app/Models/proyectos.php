<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class proyectos extends Model
{
    protected $table = 'Proyectos';
    protected $primaryKey = 'proyecto_id';
    protected $fillable = ['enfoque_id', 'cliente_id', 'vendedor_id', 'nombre', 'descripcion', 'fecha_inicio', 'fecha_fin', 'estatus_id'];
    public $timestamps = false;

    public function enfoque()
    {
        return $this->belongsTo(enfoques::class, 'enfoque_id');
    }
}
