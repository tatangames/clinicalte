<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;
    protected $table = 'paciente';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo',
        'id_estado_civil',
        'id_tipo_documento',
        'id_profesion',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'sexo',
        'referido_por',
        'num_documento',
        'correo',
        'celular',
        'telefono',
        'direccion',
        'foto',
        'numero_expediente'
    ];
}
