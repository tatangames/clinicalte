<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaEvolutiva extends Model
{
    use HasFactory;
    protected $table = 'nota_evolutiva';
    public $timestamps = false;

    protected $fillable = [
        'id_consulta',
        'id_diagnostico',
        'fecha',
        'nota'
    ];
}
