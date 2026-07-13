<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FarmaciaArticulo extends Model
{
    use HasFactory;
    protected $table = 'farmacia_articulo';
    public $timestamps = false;

    protected $fillable = [
        'id_linea',
        'id_sublinea',
        'nombre',
        'codigo_articulo',
        'existencia_minima'
    ];

    public function linea()
    {
        return $this->belongsTo(Linea::class, 'id_linea');
    }

    public function subLinea()
    {
        return $this->belongsTo(SubLinea::class, 'id_sublinea');
    }
}
