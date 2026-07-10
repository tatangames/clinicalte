<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Models\Antecedentes;
use App\Models\AntecedentesMedicos;
use App\Models\ContenidoFarmaceutica;
use App\Models\Diagnostico;
use App\Models\EstadoCivil;
use App\Models\Linea;
use App\Models\Medico;
use App\Models\Motivo;
use App\Models\MotivoFarmacia;
use App\Models\Profesion;
use App\Models\Proveedores;
use App\Models\SubLinea;
use App\Models\TipoAntecedente;
use App\Models\TipoDocumento;
use App\Models\TipoFarmaceutica;
use App\Models\TipoPaciente;
use App\Models\TipoProveedor;
use App\Models\ViaReceta;
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



    //******************* TIPO DIAGNOSTICO  *************************

    public function indexTipoDiagnostico(){
        return view('backend.admin.configuracion.tipodiagnostico.vistadiagnostico');
    }

    public function tablaTipoDiagnostico(){

        $lista = Diagnostico::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.configuracion.tipodiagnostico.tabladiagnostico', compact('lista'));
    }

    public function nuevaTipoDiagnostico(Request $request){
        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new Diagnostico();
        $dato->nombre = $request->nombre;
        $dato->descripcion = $request->descripcion;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionTipoDiagnostico(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Diagnostico::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarTipoDiagnostico(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Diagnostico::where('id', $request->id)->first()){

            Diagnostico::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }



    //******************* LINEA  *************************

    public function indexLinea(){
        return view('backend.admin.configuracion.linea.vistalinea');
    }

    public function tablaLinea(){

        $lista = Linea::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.configuracion.linea.tablalinea', compact('lista'));
    }

    public function nuevaLinea(Request $request){
        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new Linea();
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionLinea(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Linea::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarLinea(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Linea::where('id', $request->id)->first()){

            Linea::where('id', $request->id)->update([
                'nombre' => $request->nombre
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }



    //******************* SUB LINEA  *************************

    public function indexSubLinea(){
        return view('backend.admin.configuracion.sublinea.vistasublinea');
    }

    public function tablaSubLinea(){

        $lista = SubLinea::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.configuracion.sublinea.tablasublinea', compact('lista'));
    }

    public function nuevaSubLinea(Request $request){
        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new SubLinea();
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionSubLinea(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = SubLinea::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarSubLinea(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(SubLinea::where('id', $request->id)->first()){

            SubLinea::where('id', $request->id)->update([
                'nombre' => $request->nombre
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }




    //******************* PROVEEDOR *************************

    public function indexProveedor(){
        $arrayTipoProveedor = TipoProveedor::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.configuracion.proveedor.vistaproveedor', compact('arrayTipoProveedor'));
    }

    public function tablaProveedor(){

        $lista = Proveedores::with('tipoProveedor')->get();

        return view('backend.admin.configuracion.proveedor.tablaproveedor', compact('lista'));
    }

    public function nuevaProveedor(Request $request){
        $regla = array(
            'tipo' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new Proveedores();
        $dato->id_tipo_proveedor = $request->tipo;
        $dato->nombre = $request->nombre;
        $dato->nombre_comercial = $request->nombrecomercial;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionProveedor(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Proveedores::where('id', $request->id)->first()){

            $arraytipos = TipoProveedor::orderBy('nombre', 'ASC')->get();

            return ['success' => 1, 'info' => $lista, 'arraytipos' => $arraytipos];
        }else{
            return ['success' => 2];
        }
    }

    public function editarProveedor(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'tipo' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Proveedores::where('id', $request->id)->first()){

            Proveedores::where('id', $request->id)->update([
                'id_tipo_proveedor' => $request->tipo,
                'nombre' => $request->nombre,
                'nombre_comercial' => $request->nombrecomercial,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }





    // TIPO MEDICAMENTO

    public function indexTipoMedicamento(){
        $arrayTipoFarmaceutica = TipoFarmaceutica::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.configuracion.contenidofarmaceutica.vistacontenidofarmaceutica', compact('arrayTipoFarmaceutica'));
    }

    public function tablaTipoMedicamento(){

        $lista = ContenidoFarmaceutica::with('tipoFarmaceutica')->get();

        return view('backend.admin.configuracion.contenidofarmaceutica.tablacontenidofarmaceutica', compact('lista'));
    }

    public function nuevaTipoMedicamento(Request $request){
        $regla = array(
            'tipo' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new ContenidoFarmaceutica();
        $dato->id_tipo_farmaceutica = $request->tipo;
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionTipoMedicamento(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = ContenidoFarmaceutica::where('id', $request->id)->first()){

            $arraytipos = TipoFarmaceutica::orderBy('nombre', 'ASC')->get();

            return ['success' => 1, 'info' => $lista, 'arraytipos' => $arraytipos];
        }else{
            return ['success' => 2];
        }
    }

    public function editarTipoMedicamento(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'tipo' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(ContenidoFarmaceutica::where('id', $request->id)->first()){

            ContenidoFarmaceutica::where('id', $request->id)->update([
                'id_tipo_farmaceutica' => $request->tipo,
                'nombre' => $request->nombre,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }





    //******************* MOTIVO FARMACIA *************************

    public function indexMotivoFarmacia(){
        return view('backend.admin.configuracion.motivofarmacia.vistamotivofarmacia');
    }

    public function tablaMotivoFarmacia(){

        $lista = MotivoFarmacia::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.configuracion.motivofarmacia.tablamotivofarmacia', compact('lista'));
    }

    public function nuevaMotivoFarmacia(Request $request){
        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new MotivoFarmacia();
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionMotivoFarmacia(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = MotivoFarmacia::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarMotivoFarmacia(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(MotivoFarmacia::where('id', $request->id)->first()){

            MotivoFarmacia::where('id', $request->id)->update([
                'nombre' => $request->nombre
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }






    //******************* VIA RECETA  *************************

    public function indexViaReceta(){
        return view('backend.admin.configuracion.viareceta.vistaviareceta');
    }

    public function tablaViaReceta(){

        $lista = ViaReceta::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.configuracion.motivofarmacia.tablamotivofarmacia', compact('lista'));
    }

    public function nuevaViaReceta(Request $request){
        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new ViaReceta();
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionViaReceta(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = ViaReceta::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarViaReceta(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(ViaReceta::where('id', $request->id)->first()){

            ViaReceta::where('id', $request->id)->update([
                'nombre' => $request->nombre
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }










}
