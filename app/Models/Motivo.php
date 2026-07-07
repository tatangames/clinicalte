<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motivo extends Model
{
    use HasFactory;
    protected $table = 'motivo';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];
}
