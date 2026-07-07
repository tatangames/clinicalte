<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plantillas extends Model
{
    use HasFactory;
    protected $table = 'plantillas';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];
}
