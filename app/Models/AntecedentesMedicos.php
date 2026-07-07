<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AntecedentesMedicos extends Model
{
    use HasFactory;
    protected $table = 'antecedentes_medicos';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo',
        'nombre'
    ];

}
