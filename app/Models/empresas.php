<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class empresas extends Model
{
    //
    protected $table = 'empresas';
    protected $primaryKey = 'empresa_id';
    protected $fillable = ['nombre'];
    public $timestamps = false; 
}
