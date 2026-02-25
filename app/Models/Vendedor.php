<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendedor extends Model
{
    use HasFactory;

    protected $table = 'vendedores'; 
    protected $primaryKey = 'idVendedor'; 
    
    //public $timestamps = false; 

    protected $fillable = [
        'nombre',
        'apellidoPat',
        'apellidoMat',
        'correo',
        'telefono',
        'foto'
    ];
}