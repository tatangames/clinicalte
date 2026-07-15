<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antecedentes extends Model
{
    use HasFactory;
    protected $table = 'antecedentes';
    public $timestamps = false;

    protected $fillable = [
        'id_paciente',
        'id_tipeo_sanguineo',
        'antecedentes_familiares',
        'alergias',
        'medicamentos_actuales',
        'nota_antecedente_medico',
        'nota_complicaciones_diabetes',
        'nota_enfermedades_cronicas',
        'nota_antecedentes_quirurgicos',
        'antecedentes_oftalmologicos',
        'antecedentes_deportivos',
        'menarquia',
        'ciclo_menstrual',
        'pap',
        'mamografia',
        'otros'
    ];
}
