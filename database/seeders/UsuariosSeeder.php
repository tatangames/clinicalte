<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Usuario::create([
            'nombre' => 'Jonathan',
            'usuario' => 'jonathan',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('admin');

        Usuario::create([
            'nombre' => 'Giovany',
            'usuario' => 'giovany',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('admin');


        Usuario::create([
            'nombre' => 'Archivo',
            'usuario' => 'archivo',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('archivo');

        Usuario::create([
            'nombre' => 'Enfermeria',
            'usuario' => 'enfermeria',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('enfermeria');


        Usuario::create([
            'nombre' => 'Doctora',
            'usuario' => 'doctora',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('doctora');


        Usuario::create([
            'nombre' => 'Farmacia',
            'usuario' => 'farmacia',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('farmacia');
    }
}
