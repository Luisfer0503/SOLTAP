<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class cotizaciones extends Model
{
    //  
    protected $table = 'cotizaciones';
    protected $primaryKey = 'cotizacion_id';
    protected $fillable = ['proyecto_id', 'subtotal', 'envio', 'descuento', 'iva', 'total'];
}
