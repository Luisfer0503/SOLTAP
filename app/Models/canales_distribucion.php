<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class canales_distribuccion extends Model
{
    //
    protected $table = 'canales_distribucion';
    protected $primaryKey = 'canal_id'; 
    protected $fillable = ['nombre', 'descripcion'];
    public $timestamps = false; 
}
