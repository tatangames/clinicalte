<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContenidoFarmaceutica extends Model
{
    use HasFactory;
    protected $table = 'contenido_farmaceutica';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_farmaceutica',
        'nombre'
    ];

    public function tipoFarmaceutica()
    {
        return $this->belongsTo(TipoFarmaceutica::class, 'id_tipo_farmaceutica');
    }
}
