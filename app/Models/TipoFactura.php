<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoFactura extends Model
{
    use HasFactory;
    protected $table = 'tipo_factura';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];
}
