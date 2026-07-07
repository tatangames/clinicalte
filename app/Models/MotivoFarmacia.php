<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotivoFarmacia extends Model
{
    use HasFactory;
    protected $table = 'tipo_paciente';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];
}
