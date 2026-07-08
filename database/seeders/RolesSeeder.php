<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Administrador
        $roleAdmin = Role::create(['name' => 'admin', 'guard_name' => 'admin']);

        // Archivo
        $roleArchivo = Role::create(['name' => 'archivo', 'guard_name' => 'admin']);

        // Enfermeria
        $roleEnfermeria = Role::create(['name' => 'enfermeria', 'guard_name' => 'admin']);

        // Doctora
        $roleDoctora = Role::create(['name' => 'doctora', 'guard_name' => 'admin']);

        // Farmacia
        $roleFarmacia = Role::create(['name' => 'farmacia', 'guard_name' => 'admin']);


        // solo para administrador
        Permission::create(['name' => 'sidebar.roles.y.permisos', 'description' => 'sidebar seccion roles y permisos'])->syncRoles($roleAdmin);

    }
}
