<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipeoSanguineo extends Model
{
    use HasFactory;
    protected $table = 'tipeo_sanguineo';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];
}
