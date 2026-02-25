<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class cotizaciones extends Model
{
    //  
    protected $table = 'cotizaciones';
    protected $primaryKey = 'cotizacion_id';
    protected $fillable = ['prospecto_id', 'cliente_id', 'articulo_id', 'vendedor_id', 'precio', 'fecha_cotizacion', 'estatus_id', 'descripcion'];
    public $timestamps = false;
}
