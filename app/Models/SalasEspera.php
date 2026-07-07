<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalasEspera extends Model
{
    use HasFactory;
    protected $table = 'salas_espera';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];
}
