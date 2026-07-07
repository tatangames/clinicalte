<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    use HasFactory;
    protected $table = 'medico';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'nombre',
        'apellido',
        'telefono'
    ];
}
