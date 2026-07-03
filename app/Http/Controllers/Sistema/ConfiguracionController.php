<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Models\TipoPaciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ConfiguracionController extends Controller
{

    //******************* TIPO PACIENTE *************************
    public function indexTipoPaciente(){
        return view('backend.admin.configuracion.tipopaciente.vistatipopaciente');
    }

    public function tablaTipoPaciente(){

        $lista = TipoPaciente::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.configuracion.tipopaciente.tablatipopaciente', compact('lista'));
    }

    public function nuevaTipoPaciente(Request $request){
        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new TipoPaciente();
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionTipoPaciente(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = TipoPaciente::where('id', $request->id)->first()){

            return ['success' => 1, 'nombre' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarTipoPaciente(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(TipoPaciente::where('id', $request->id)->first()){

            TipoPaciente::where('id', $request->id)->update([
                'nombre' => $request->nombre
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }





























}
