<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntradaMedicamentoDetalle extends Model
{
    use HasFactory;
    protected $table = 'entrada_medicamento_detalle';
    public $timestamps = false;

    protected $fillable = [
        'id_entrada_medicamento',
        'id_medicamento',
        'nombre_copia',
        'cantidad',
        'cantidad_fija',
        'precio',
        'lote',
        'fecha_vencimiento',
        'precio_donacion'
    ];

}
