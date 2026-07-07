<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoFarmaceutica extends Model
{
    use HasFactory;
    protected $table = 'tipo_farmaceutica';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];
}
