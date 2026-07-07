<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubLinea extends Model
{
    use HasFactory;
    protected $table = 'sub_linea';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];
}
