<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PacienteAntecedentes extends Model
{
    use HasFactory;
    protected $table = 'paciente_antecedentes';
    public $timestamps = false;

    protected $fillable = [
        'id_paciente',
        'id_antecedente_medico',
    ];

}
