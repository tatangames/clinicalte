<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecetaDetalle extends Model
{
    use HasFactory;
    protected $table = 'recetas_detalle';
    public $timestamps = false;

    protected $fillable = [
        'id_recetas',
        'id_entrada_detalle',
        'id_via',
        'cantidad',
        'descripcion',
    ];
}
