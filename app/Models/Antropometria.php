<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antropometria extends Model
{
    use HasFactory;
    protected $table = 'antropometria';
    public $timestamps = false;

    protected $fillable = [
        'id_consulta',
        'id_usuario',
        'fecha',
        'hora',
        'frecuencia_cardiaca',
        'frecuencia_respiratoria',
        'presion_arterial',
        'temperatura',
        'perim_abdominal',
        'perim_cefalico',
        'peso_libra',
        'peso_kilo',
        'estatura',
        'imc',
        'resultado_imc',
        'glucometria_capilar',
        'glicohemoglibona_capilar',
        'cetona_capilar',
        'spo2',
        'perim_cintura',
        'perim_cadera',
        'icc',
        'riesgo_mujer',
        'riesgo_hombre',
        'gasto_energetico_basal',
        'nota_adicional'
    ];
}
