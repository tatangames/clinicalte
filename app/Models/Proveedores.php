<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedores extends Model
{
    use HasFactory;
    protected $table = 'proveedores';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_proveedor',
        'nombre',
        'nombre_comercial',
        'nrc',
        'nit',
        'direccion',
        'departamento_contacto',
        'telefono_fijo',
        'telefono_celular',
        'correo'
    ];
}
