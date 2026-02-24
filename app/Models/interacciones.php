<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class interacciones extends Model
{
    protected $table = 'interacciones';
    protected $primaryKey = 'interaccion_id';
    protected $fillable = ['nombre'];
    public $timestamps = false; 
}
