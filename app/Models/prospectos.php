<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class prospectos extends Model
{
    protected $table = 'prospectos';
    protected $primaryKey = 'prospecto_id';
    protected $fillable = ['nombre', 'apellido', 'email', 'telefono', 'empresa', 'estatus_id'];
    public $timestamps = false;
}
