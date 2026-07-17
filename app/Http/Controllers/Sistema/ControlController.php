<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ControlController extends Controller
{
    public function indexRedireccionamiento(){
        $user = Auth::user();

        // ADMINISTRADOR
        if($user->hasRole('admin')){
            return redirect()->route('admin.roles.index');
        }

        // Inventario
        else if($user->hasRole('archivo')){
            return redirect()->route('admin.asignaciones.index');
        }

        // Enfermeria
        else if($user->hasRole('enfermeria')){
            return redirect()->route('admin.asignaciones.index');
        }

        // Doctora
        else if($user->hasRole('doctora')){
            return redirect()->route('admin.asignaciones.index');
        }

        // Farmacia
        else if($user->hasRole('farmacia')){
            return redirect()->route('admin.asignaciones.index');
        }

        return redirect()->route('no.permisos.index');
    }

    public function indexSinPermiso(){
        return view('errors.403');
    }

}
