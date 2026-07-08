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






}); // end auth





