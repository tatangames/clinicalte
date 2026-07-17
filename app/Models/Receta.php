<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    use HasFactory;
    protected $table = 'recetas';
    public $timestamps = false;

    protected $fillable = [
        'id_consulta',
        'id_paciente',
        'id_diagnostico',
        'id_usuario',
        'descripcion_general',
        'fecha',
        'proxima_cita',
        'estado',
        'fecha_estado',
        'nota_denegada',
        'id_usuario_estado'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class, 'id_diagnostico');
    }
}
