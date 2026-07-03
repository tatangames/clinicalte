<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPaciente extends Model
{
    use HasFactory;
    protected $table = 'tipo_paciente';
    public $timestamps = false;
}
