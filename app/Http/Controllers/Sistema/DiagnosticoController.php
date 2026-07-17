<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Models\Antecedentes;
use App\Models\AntecedentesMedicos;
use App\Models\Antropometria;
use App\Models\ArticuloMedicamento;
use App\Models\ConsultaPaciente;
use App\Models\ContenidoFarmaceutica;
use App\Models\CuadroClinico;
use App\Models\Diagnostico;
use App\Models\EstadoCivil;
use App\Models\FarmaciaArticulo;
use App\Models\FuenteFinanciamiento;
use App\Models\Linea;
use App\Models\Medico;
use App\Models\Motivo;
use App\Models\MotivoFarmacia;
use App\Models\Paciente;
use App\Models\PacienteAntecedentes;
use App\Models\Profesion;
use App\Models\Proveedores;
use App\Models\Receta;
use App\Models\RecetaDetalle;
use App\Models\SalasEspera;
use App\Models\SubLinea;
use App\Models\TipeoSanguineo;
use App\Models\TipoAntecedente;
use App\Models\TipoDocumento;
use App\Models\TipoFarmaceutica;
use App\Models\TipoPaciente;
use App\Models\TipoProveedor;
use App\Models\Usuario;
use App\Models\ViaReceta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DiagnosticoController extends Controller
{
    public function registroExtraDiagnostico(Request $request){

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $registro = new Diagnostico();
        $registro->nombre = $request->nombre;
        $registro->descripcion = $request->descripcion;

        if($registro->save()){

            $arrayLista = Diagnostico::orderBy('nombre', 'ASC')->get();

            return ['success' => 1, 'lista' => $arrayLista];
        }else{
            return ['success' => 99];
        }
    }


    public function registroExtraVia(Request $request){

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $registro = new ViaReceta();
        $registro->nombre = $request->nombre;

        if($registro->save()){

            $arrayLista = ViaReceta::orderBy('nombre', 'ASC')->get();

            return ['success' => 1, 'lista' => $arrayLista];
        }else{
            return ['success' => 99];
        }
    }

}
