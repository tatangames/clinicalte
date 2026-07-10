<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Sistema\LoginController;
use App\Http\Controllers\Sistema\ControlController;
use App\Http\Controllers\Sistema\RolesController;
use App\Http\Controllers\Sistema\PerfilController;
use App\Http\Controllers\Sistema\PermisoController;
use App\Http\Controllers\Sistema\ConfiguracionController;
use App\Http\Controllers\Sistema\SalidasController;
use App\Http\Controllers\Sistema\HistorialController;
use App\Http\Controllers\Sistema\ReportesController;
use App\Http\Controllers\Sistema\MaterialesController;
use App\Http\Controllers\Sistema\RegistrosController;
use App\Http\Controllers\Sistema\UnidadEmpleadoController;
use App\Http\Controllers\Sistema\EmpleadoController;
use App\Http\Controllers\Sistema\HistorialSalidasController;


Route::get('/', [LoginController::class,'vistaLoginForm'])->name('login.admin');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');

Route::middleware('auth:admin')->group(function () {

    // --- ROLES ---
    Route::get('/admin/roles/index', [RolesController::class,'index'])->name('admin.roles.index');
    Route::get('/admin/roles/tabla', [RolesController::class,'tablaRoles']);
    Route::get('/admin/roles/lista/permisos/{id}', [RolesController::class,'vistaPermisos']);
    Route::get('/admin/roles/permisos/tabla/{id}', [RolesController::class,'tablaRolesPermisos']);
    Route::post('/admin/roles/permiso/borrar', [RolesController::class, 'borrarPermiso']);
    Route::post('/admin/roles/permiso/agregar', [RolesController::class, 'agregarPermiso']);
    Route::get('/admin/roles/permisos/lista', [RolesController::class,'listaTodosPermisos']);
    Route::get('/admin/roles/permisos-todos/tabla', [RolesController::class,'tablaTodosPermisos']);
    Route::post('/admin/roles/borrar-global', [RolesController::class, 'borrarRolGlobal']);

    // --- PERMISOS ---
    Route::get('/admin/permisos/index', [PermisoController::class,'index'])->name('admin.permisos.index');
    Route::get('/admin/permisos/tabla', [PermisoController::class,'tablaUsuarios']);
    Route::post('/admin/permisos/nuevo-usuario', [PermisoController::class, 'nuevoUsuario']);
    Route::post('/admin/permisos/info-usuario', [PermisoController::class, 'infoUsuario']);
    Route::post('/admin/permisos/editar-usuario', [PermisoController::class, 'editarUsuario']);
    Route::post('/admin/permisos/nuevo-rol', [PermisoController::class, 'nuevoRol']);
    Route::post('/admin/permisos/extra-nuevo', [PermisoController::class, 'nuevoPermisoExtra']);
    Route::post('/admin/permisos/extra-borrar', [PermisoController::class, 'borrarPermisoGlobal']);

    // --- PERFIL ---
    Route::get('/admin/editar-perfil/index', [PerfilController::class,'indexEditarPerfil'])->name('admin.perfil');
    Route::post('/admin/editar-perfil/actualizar', [PerfilController::class, 'editarUsuario']);

    Route::get('sin-permisos', [ControlController::class,'indexSinPermiso'])->name('no.permisos.index');

    // --- CONTROL WEB ---
    Route::get('/panel', [ControlController::class,'indexRedireccionamiento'])->name('admin.panel');


    // --- TIPO DE PACIENTE ---
    Route::get('/admin/tipopaciente/index', [ConfiguracionController::class,'indexTipoPaciente'])->name('admin.tipopaciente.index');
    Route::get('/admin/tipopaciente/tabla/index', [ConfiguracionController::class,'tablaTipoPaciente']);
    Route::post('/admin/tipopaciente/nuevo', [ConfiguracionController::class, 'nuevaTipoPaciente']);
    Route::post('/admin/tipopaciente/informacion', [ConfiguracionController::class, 'informacionTipoPaciente']);
    Route::post('/admin/tipopaciente/editar', [ConfiguracionController::class, 'editarTipoPaciente']);

    // --- PROFESION ---
    Route::get('/admin/profesion/index', [ConfiguracionController::class,'indexProfesion'])->name('admin.profesion.index');
    Route::get('/admin/profesion/tabla/index', [ConfiguracionController::class,'tablaProfesion']);
    Route::post('/admin/profesion/nuevo', [ConfiguracionController::class, 'nuevaProfesion']);
    Route::post('/admin/profesion/informacion', [ConfiguracionController::class, 'informacionProfesion']);
    Route::post('/admin/profesion/editar', [ConfiguracionController::class, 'editarProfesion']);

    // --- ESTADO CIVIL ---
    Route::get('/admin/estadocivil/index', [ConfiguracionController::class,'indexEstadoCivil'])->name('admin.estadocivil.index');
    Route::get('/admin/estadocivil/tabla/index', [ConfiguracionController::class,'tablaEstadoCivil']);
    Route::post('/admin/estadocivil/nuevo', [ConfiguracionController::class, 'nuevaEstadoCivil']);
    Route::post('/admin/estadocivil/informacion', [ConfiguracionController::class, 'informacionEstadoCivil']);
    Route::post('/admin/estadocivil/editar', [ConfiguracionController::class, 'editarEstadoCivil']);

    // --- ESTADO CIVIL ---
    Route::get('/admin/estadocivil/index', [ConfiguracionController::class,'indexEstadoCivil'])->name('admin.estadocivil.index');
    Route::get('/admin/estadocivil/tabla/index', [ConfiguracionController::class,'tablaEstadoCivil']);
    Route::post('/admin/estadocivil/nuevo', [ConfiguracionController::class, 'nuevaEstadoCivil']);
    Route::post('/admin/estadocivil/informacion', [ConfiguracionController::class, 'informacionEstadoCivil']);
    Route::post('/admin/estadocivil/editar', [ConfiguracionController::class, 'editarEstadoCivil']);

    // --- ANTECEDENTES MEDICOS ---
    Route::get('/admin/antecedentesmedicos/index', [ConfiguracionController::class,'indexAntecedentesMedicos'])->name('admin.antecedentesmedicos.index');
    Route::get('/admin/antecedentesmedicos/tabla/index', [ConfiguracionController::class,'tablaAntecedentesMedicos']);
    Route::post('/admin/antecedentesmedicos/nuevo', [ConfiguracionController::class, 'nuevaAntecedentesMedicos']);
    Route::post('/admin/antecedentesmedicos/informacion', [ConfiguracionController::class, 'informacionAntecedentesMedicos']);
    Route::post('/admin/antecedentesmedicos/editar', [ConfiguracionController::class, 'editarAntecedentesMedicos']);

    // --- MOTIVO CONSULTA ---
    Route::get('/admin/motivoconsulta/index', [ConfiguracionController::class,'indexMotivoConsulta'])->name('admin.motivoconsulta.index');
    Route::get('/admin/motivoconsulta/tabla/index', [ConfiguracionController::class,'tablaMotivoConsulta']);
    Route::post('/admin/motivoconsulta/nuevo', [ConfiguracionController::class, 'nuevaMotivoConsulta']);
    Route::post('/admin/motivoconsulta/informacion', [ConfiguracionController::class, 'informacionMotivoConsulta']);
    Route::post('/admin/motivoconsulta/editar', [ConfiguracionController::class, 'editarMotivoConsulta']);

    // --- TIPO DOCUMENTO ---
    Route::get('/admin/tipodocumento/index', [ConfiguracionController::class,'indexTipoDocumento'])->name('admin.tipodocumento.index');
    Route::get('/admin/tipodocumento/tabla/index', [ConfiguracionController::class,'tablaTipoDocumento']);
    Route::post('/admin/tipodocumento/nuevo', [ConfiguracionController::class, 'nuevaTipoDocumento']);
    Route::post('/admin/tipodocumento/informacion', [ConfiguracionController::class, 'informacionTipoDocumento']);
    Route::post('/admin/tipodocumento/editar', [ConfiguracionController::class, 'editarTipoDocumento']);

    // --- TIPO DIAGNOSTICO ---
    Route::get('/admin/tipodiagnostico/index', [ConfiguracionController::class,'indexTipoDiagnostico'])->name('admin.tipodiagnostico.index');
    Route::get('/admin/tipodiagnostico/tabla/index', [ConfiguracionController::class,'tablaTipoDiagnostico']);
    Route::post('/admin/tipodiagnostico/nuevo', [ConfiguracionController::class, 'nuevaTipoDiagnostico']);
    Route::post('/admin/tipodiagnostico/informacion', [ConfiguracionController::class, 'informacionTipoDiagnostico']);
    Route::post('/admin/tipodiagnostico/editar', [ConfiguracionController::class, 'editarTipoDiagnostico']);

    // --- LINEA ---
    Route::get('/admin/linea/index', [ConfiguracionController::class,'indexLinea'])->name('admin.linea.index');
    Route::get('/admin/linea/tabla/index', [ConfiguracionController::class,'tablaLinea']);
    Route::post('/admin/linea/nuevo', [ConfiguracionController::class, 'nuevaLinea']);
    Route::post('/admin/linea/informacion', [ConfiguracionController::class, 'informacionLinea']);
    Route::post('/admin/linea/editar', [ConfiguracionController::class, 'editarLinea']);

    // --- SUB LINEA ---
    Route::get('/admin/sublinea/index', [ConfiguracionController::class,'indexSubLinea'])->name('admin.sublinea.index');
    Route::get('/admin/sublinea/tabla/index', [ConfiguracionController::class,'tablaSubLinea']);
    Route::post('/admin/sublinea/nuevo', [ConfiguracionController::class, 'nuevaSubLinea']);
    Route::post('/admin/sublinea/informacion', [ConfiguracionController::class, 'informacionSubLinea']);
    Route::post('/admin/sublinea/editar', [ConfiguracionController::class, 'editarSubLinea']);

    // --- PROVEEDOR ---
    Route::get('/admin/proveedor/index', [ConfiguracionController::class,'indexProveedor'])->name('admin.proveedor.index');
    Route::get('/admin/proveedor/tabla/index', [ConfiguracionController::class,'tablaProveedor']);
    Route::post('/admin/proveedor/nuevo', [ConfiguracionController::class, 'nuevaProveedor']);
    Route::post('/admin/proveedor/informacion', [ConfiguracionController::class, 'informacionProveedor']);
    Route::post('/admin/proveedor/editar', [ConfiguracionController::class, 'editarProveedor']);

    // --- TIPO MEDICAMENTO ---
    Route::get('/admin/contenidofarmaceutica/index', [ConfiguracionController::class,'indexTipoMedicamento'])->name('admin.contenidofarmaceutica.index');
    Route::get('/admin/contenidofarmaceutica/tabla/index', [ConfiguracionController::class,'tablaTipoMedicamento']);
    Route::post('/admin/contenidofarmaceutica/nuevo', [ConfiguracionController::class, 'nuevaTipoMedicamento']);
    Route::post('/admin/contenidofarmaceutica/informacion', [ConfiguracionController::class, 'informacionTipoMedicamento']);
    Route::post('/admin/contenidofarmaceutica/editar', [ConfiguracionController::class, 'editarTipoMedicamento']);

    // --- MOTIVO FARMACIA ---
    Route::get('/admin/motivofarmacia/index', [ConfiguracionController::class,'indexMotivoFarmacia'])->name('admin.motivofarmacia.index');
    Route::get('/admin/motivofarmacia/tabla/index', [ConfiguracionController::class,'tablaMotivoFarmacia']);
    Route::post('/admin/motivofarmacia/nuevo', [ConfiguracionController::class, 'nuevaMotivoFarmacia']);
    Route::post('/admin/motivofarmacia/informacion', [ConfiguracionController::class, 'informacionMotivoFarmacia']);
    Route::post('/admin/motivofarmacia/editar', [ConfiguracionController::class, 'editarMotivoFarmacia']);

    // --- VIA RECETA ---
    Route::get('/admin/viareceta/index', [ConfiguracionController::class,'indexViaReceta'])->name('admin.viareceta.index');
    Route::get('/admin/viareceta/tabla/index', [ConfiguracionController::class,'tablaViaReceta']);
    Route::post('/admin/viareceta/nuevo', [ConfiguracionController::class, 'nuevaViaReceta']);
    Route::post('/admin/viareceta/informacion', [ConfiguracionController::class, 'informacionViaReceta']);
    Route::post('/admin/viareceta/editar', [ConfiguracionController::class, 'editarViaReceta']);





}); // end auth





