<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViaReceta extends Model
{
    use HasFactory;
    protected $table = 'via_receta';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];
}
