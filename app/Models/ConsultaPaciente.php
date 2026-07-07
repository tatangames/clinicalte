<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultaPaciente extends Model
{
    use HasFactory;
    protected $table = 'consulta_paciente';
    public $timestamps = false;

    protected $fillable = [
        'id_paciente',
        'id_motivo',
        'id_salaespera',
        'fecha_hora',
        'estado_paciente',
        'hora_dentrosala',
        'estado_receta',
    ];
}
