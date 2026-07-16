<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalidaRecetaDetalle extends Model
{
    use HasFactory;
    protected $table = 'salida_receta_detalle';
    public $timestamps = false;

    protected $fillable = [
        'id_salidareceta',
        'id_entrada_detalle',
        'cantidad',
    ];

}
