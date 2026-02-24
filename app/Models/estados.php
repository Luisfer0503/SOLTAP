<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class estados extends Model
{
    protected $table = 'estados';
    protected $primaryKey = 'estado_id';
    protected $fillable = ['nombre'];
    public $timestamps = false; 
}
