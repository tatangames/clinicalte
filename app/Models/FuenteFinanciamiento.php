<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuenteFinanciamiento extends Model
{
    use HasFactory;
    protected $table = 'fuente_financiamiento';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];
}
