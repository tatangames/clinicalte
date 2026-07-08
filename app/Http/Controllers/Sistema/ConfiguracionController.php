<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Models\Antecedentes;
use App\Models\AntecedentesMedicos;
use App\Models\EstadoCivil;
use App\Models\Medico;
use App\Models\Motivo;
use App\Models\Profesion;
use App\Models\TipoAntecedente;
use App\Models\TipoDocumento;
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

            return ['success' => 1, 'info' => $lista];
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




    //******************* PROFESION *************************

    public function indexProfesion(){
        return view('backend.admin.configuracion.profesion.vistaprofesion');
    }

    public function tablaProfesion(){

        $lista = Profesion::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.configuracion.profesion.tablaprofesion', compact('lista'));
    }

    public function nuevaProfesion(Request $request){
        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new Profesion();
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionProfesion(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Profesion::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarProfesion(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Profesion::where('id', $request->id)->first()){

            Profesion::where('id', $request->id)->update([
                'nombre' => $request->nombre
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }




    //******************* ESTADO CIVIL *************************

    public function indexEstadoCivil(){
        return view('backend.admin.configuracion.estadocivil.vistaestadocivil');
    }

    public function tablaEstadoCivil(){

        $lista = EstadoCivil::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.configuracion.estadocivil.tablaestadocivil', compact('lista'));
    }

    public function nuevaEstadoCivil(Request $request){
        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new EstadoCivil();
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionEstadoCivil(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = EstadoCivil::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarEstadoCivil(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(EstadoCivil::where('id', $request->id)->first()){

            EstadoCivil::where('id', $request->id)->update([
                'nombre' => $request->nombre
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }




    //******************* ANTECEDENTES MEDICOS *************************

    public function indexAntecedentesMedicos(){
        $arrayTipoAnte = TipoAntecedente::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.configuracion.antecedentesmedicos.vistaantecedentesmedicos', compact('arrayTipoAnte'));
    }

    public function tablaAntecedentesMedicos(){

        $lista = AntecedentesMedicos::orderBy('nombre', 'ASC')->get();

        foreach ($lista as $dato){

            $infoAnte = TipoAntecedente::where('id', $dato->id_tipo)->first();
            $dato->tipoantecedente = $infoAnte->nombre;
        }
        return view('backend.admin.configuracion.antecedentesmedicos.tablaantecedentesmedicos', compact('lista'));
    }

    public function nuevaAntecedentesMedicos(Request $request){
        $regla = array(
            'tipo' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new AntecedentesMedicos();
        $dato->nombre = $request->nombre;
        $dato->id_tipo = $request->tipo;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionAntecedentesMedicos(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = AntecedentesMedicos::where('id', $request->id)->first()){

            $arraytipos = TipoAntecedente::orderBy('nombre', 'ASC')->get();

            return ['success' => 1, 'info' => $lista, 'arraytipos' => $arraytipos];
        }else{
            return ['success' => 2];
        }
    }

    public function editarAntecedentesMedicos(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'tipo' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(AntecedentesMedicos::where('id', $request->id)->first()){

            AntecedentesMedicos::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'id_tipo' => $request->tipo
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }





    //******************* MOTIVO CONSULTA *************************

    public function indexMotivoConsulta(){
        return view('backend.admin.configuracion.motivoconsulta.vistamotivoconsulta');
    }

    public function tablaMotivoConsulta(){

        $lista = Motivo::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.configuracion.motivoconsulta.tablamotivoconsulta', compact('lista'));
    }

    public function nuevaMotivoConsulta(Request $request){
        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new Motivo();
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionMotivoConsulta(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Motivo::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarMotivoConsulta(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Motivo::where('id', $request->id)->first()){

            Motivo::where('id', $request->id)->update([
                'nombre' => $request->nombre
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }





    //******************* TIPO DOCUMENTOS  *************************

    public function indexTipoDocumento(){
        return view('backend.admin.configuracion.tipodocumento.vistatipodocumento');
    }

    public function tablaTipoDocumento(){

        $lista = TipoDocumento::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.configuracion.tipodocumento.tablatipodocumento', compact('lista'));
    }

    public function nuevaTipoDocumento(Request $request){
        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new TipoDocumento();
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionTipoDocumento(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = TipoDocumento::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarTipoDocumento(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(TipoDocumento::where('id', $request->id)->first()){

            TipoDocumento::where('id', $request->id)->update([
                'nombre' => $request->nombre
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }





}
