<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticuloMedicamento extends Model
{
    use HasFactory;
    protected $table = 'articulo_medicamento';
    public $timestamps = false;

    protected $fillable = [
        'id_farmacia_articulo',
        'id_con_far_envase',

        'id_con_far_forma',
        'id_con_far_concentracion',
        'id_con_far_contenido',
        'id_con_far_administra',
        'nombre_generico',
    ];
}
