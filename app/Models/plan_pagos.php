<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class plan_pagos extends Model
{
    protected $table = 'plan_pagos';
    protected $fillable = ['cotizacion_id', 'nombre', 'numero_pago', 'total_pagos_plan', 'porcentaje', 'monto', 'monto_pagado', 'estatus', 'fecha_pago_real'];
}