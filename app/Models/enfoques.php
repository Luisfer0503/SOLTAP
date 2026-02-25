<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class enfoques extends Model
{
    //
    protected $table = 'enfoques';
    protected $primaryKey = 'enfoque_id';
    protected $fillable = ['nombre', 'descripcion'];
    public $timestamps = false; 
}
