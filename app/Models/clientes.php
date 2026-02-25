<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class clientes extends Model
{
    //
    protected $table = 'clientes';
    protected $primaryKey = 'cliente_id';
    protected $fillable = ['nombre', 'apellido_paterno', 'apellido_materno', 'correo', 'telefono', 'calle', 'municipio', 'direccion_entrega', 'maps', 'estatus_id', 'empresa_id', 'canal_id', 'estado_id', 'enfoque_id', 'descripcion', 'fecha'];
    public $timestamps = false; 
}
