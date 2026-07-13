<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntradaMedicamento extends Model
{
    use HasFactory;
    protected $table = 'entrada_medicamento';
    public $timestamps = false;

    protected $fillable = [
        'id_tipofactura',
        'id_fuentefina',
        'id_proveedor',
        'id_usuario',
        'fecha',
        'numero_factura'
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedores::class, 'id_proveedor');
    }

    public function fuenteFinanciamiento()
    {
        return $this->belongsTo(FuenteFinanciamiento::class, 'id_fuentefina');
    }

    public function tipoFactura()
    {
        return $this->belongsTo(TipoFactura::class, 'id_tipofactura');
    }

}
