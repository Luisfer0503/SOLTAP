<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class estatus extends Model
{
    protected $table = 'estatus';
    protected $primaryKey = 'estatus_id';
    protected $fillable = ['nombre'];
    public $timestamps = false; 
}
