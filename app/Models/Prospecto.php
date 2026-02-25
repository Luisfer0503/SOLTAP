<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prospecto extends Model
{
    use HasFactory;

    protected $table = 'prospectos';
    protected $primaryKey = 'prospecto_id';

    protected $fillable = [
        'nombre', 'apellido_paterno', 'apellido_materno',
        'correo', 'telefono', 'calle', 'municipio', 'direccion_entrega',
        'maps', 'estatus_id', 'empresa_id', 'canal_id', 'estado_id', 'enfoque_id',
        'descripcion', 'fecha', 'proyecto'
    ];
    
    public $timestamps = false;
}