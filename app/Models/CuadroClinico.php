<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuadroClinico extends Model
{
    use HasFactory;
    protected $table = 'cuadro_clinico';
    public $timestamps = false;

    protected $fillable = [
        'id_consulta',
        'id_diagnostico',
        'id_usuario',
        'descripcion'
    ];
}
