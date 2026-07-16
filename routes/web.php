<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Sistema\LoginController;
use App\Http\Controllers\Sistema\ControlController;
use App\Http\Controllers\Sistema\RolesController;
use App\Http\Controllers\Sistema\PerfilController;
use App\Http\Controllers\Sistema\PermisoController;
use App\Http\Controllers\Sistema\ConfiguracionController;
use App\Http\Controllers\Sistema\FarmaciaController;
use App\Http\Controllers\Sistema\CatalogoController;
use App\Http\Controllers\Sistema\ExpedienteController;
use App\Http\Controllers\Sistema\DocumentoRecetaController;
use App\Http\Controllers\Sistema\AsignacionesController;
use App\Http\Controllers\Sistema\HistorialClinicoController;
use App\Http\Controllers\Sistema\NotasController;
use App\Http\Controllers\Sistema\RecetasController;
use App\Http\Controllers\Sistema\ReportesController;
use App\Http\Controllers\Sistema\SalidaRecetaController;
use App\Http\Controllers\Sistema\SalidaManualController;


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

    // --- REGISTRO PRIMER ARTICULO EN FARMACIA---
    Route::get('/admin/farmacia/registrar/index', [FarmaciaController::class,'indexRegistroArticulo'])->name('admin.registrararticulo.index');
    Route::post('/admin/farmacia/registrar/nuevo', [FarmaciaController::class, 'registrarArticulo']);
    // guardar y obtener listado de Contenido Farmaceutica
    Route::post('/admin/guardar/contenidofarma/get/listado', [FarmaciaController::class, 'guardarExtraContenidoFarmaceutica']);

    // --- INGRESO DE ARTICULO FARMACIA
    Route::get('/admin/farmacia/ingreso/articulo/index', [FarmaciaController::class,'indexIngresoArticulo'])->name('admin.farmacia.ingreso.articulo');
    Route::post('/admin/buscar/nombre/medicamento',  [FarmaciaController::class,'buscarMedicamento']);
    Route::post('/admin/registrar/nuevo/medicamento',  [FarmaciaController::class,'registrarNuevoMedicamento']);
    // actualizar y agregar extra articulos a una entrada
    Route::post('/admin/registrar/actualizar/medicamento',  [FarmaciaController::class,'actualizarNuevoMedicamento']);

    // --- REGISTRO PRIMER ARTICULO EN FARMACIA---
    Route::get('/admin/catalogo/index', [CatalogoController::class,'indexCatalogo'])->name('admin.catalogo.index');
    Route::get('/admin/catalogo/tabla/index', [CatalogoController::class, 'tablaCatalogo']);
    // vista editar catalogo
    Route::get('/admin/catalogo/individual/vista/editar/{idarticulo}', [CatalogoController::class, 'vistaEditarArticuloCatalogo']);
    Route::post('/admin/catalogo/individual/actualizar', [CatalogoController::class, 'actualizarArticuloCatalogo']);

    // --- HISTORIAL ENTRADAS ---
    Route::get('/admin/historialentradas/index', [CatalogoController::class,'indexHistorialEntradas'])->name('admin.historialentradas.index');
    Route::get('/admin/historialentradas/tabla/index', [CatalogoController::class, 'tablaHistorialEntradas']);
    Route::get('/admin/vista/editar/info/entrada/{identrada}', [CatalogoController::class,'vistaEditarEntrada']);
    Route::post('/admin/modificar/entrada/medicamento/detalle', [CatalogoController::class,'informacionEntradaMediDetalle']);
    Route::post('/admin/actualizar/entrada/medicamento/detalle', [CatalogoController::class,'actualizarEntradaMediDetalle']);

    // --- EXPEDIENTE ---
    Route::get('/admin/expediente/index', [ExpedienteController::class,'indexNuevoExpediente'])->name('admin.nuevoexpediente.index');
    Route::post('/admin/expediente/registro', [ExpedienteController::class, 'nuevoExpediente']);

    // --- BUSCAR EXPEDIENTE ---
    Route::get('/admin/buscarexpediente/index', [ExpedienteController::class,'indexBuscarExpediente'])->name('admin.buscarexpediente.index');
    Route::get('/admin/buscarexpediente/tabla/index', [ExpedienteController::class, 'tablaBuscarExpediente']);
    Route::get('/admin/pdf/reporte/fichapaciente/general/{idpaciente}', [ExpedienteController::class,'generarReporteFichaGeneralPaciente']);

    Route::get('/admin/asignaciones/info/vista/editarpaciente/{idpaciente}', [ExpedienteController::class,'indexEditarPaciente']);
    Route::post('/admin/expediente/actualizar', [ExpedienteController::class, 'actualizarExpediente']);
    Route::get('/admin/documentoreceta/vista/{idpaciente}', [DocumentoRecetaController::class, 'indexDocumentosRecetas']);
    // antecedentes todos por paciente
    Route::get('/admin/documentoreceta/bloque/antecedentes/{idpaciente}', [DocumentoRecetaController::class, 'tablaAntecedentesPorPaciente']);
    // antropometria sv todos por paciente
    Route::get('/admin/documentoreceta/bloque/antropometriasv/{idpaciente}', [DocumentoRecetaController::class, 'tablaAntropometriaPorPaciente']);
    // todas las recetas para un paciente
    Route::get('/admin/documentoreceta/bloque/recetas/{idpaciente}', [DocumentoRecetaController::class, 'tablaRecetasPorPaciente']);
    // todos los cuadros clinicos de un paciente
    Route::get('/admin/documentoreceta/bloque/cuadroclinico/{idpaciente}', [DocumentoRecetaController::class, 'tablaCuadroClinicoPorPaciente']);









    // --- ASIGNACIONES ---
    Route::get('/admin/asignaciones/vista/index', [AsignacionesController::class,'indexAsignaciones'])->name('admin.asignaciones.index');
    Route::post('/admin/asignaciones/buscar/paciente',  [AsignacionesController::class,'buscadorPaciente']);
    Route::post('/admin/asignaciones/nuevo/registro',  [AsignacionesController::class,'nuevoRegistro']);
    Route::get('/admin/asignaciones/paciente/esperando', [AsignacionesController::class,'tablaPacientesEnEspera']);
    Route::get('/admin/asignaciones/tablamodal/enfermeria', [AsignacionesController::class, 'tablaModalEnfermeria']);
    Route::get('/admin/asignaciones/tablamodal/consultoria', [AsignacionesController::class, 'tablaModalConsultoria']);
    Route::post('/admin/asignaciones/informacion/paciente',  [AsignacionesController::class,'informacionPaciente']);
    Route::post('/admin/asignaciones/informacion/guardar',  [AsignacionesController::class,'guardarInformacionEditadaPaciente']);
    Route::post('/admin/asignaciones/finalizar/consulta',  [AsignacionesController::class,'finalizarConsultaPaciente']);
    Route::post('/admin/asignaciones/ingresar/paciente/sala',  [AsignacionesController::class,'ingresarPacienteALaSala']);


// devuelve lista de personas que estan dentro de una x sala
    Route::post('/admin/asignaciones/personas/sala',  [AsignacionesController::class,'personasDentroSala']);






// Informacion del paciente que esta dentro de la sala, informacion para el modal.
// Ficha Administrativa
    Route::post('/admin/asignaciones/info/paciente/dentrosala',  [AsignacionesController::class,'informacionPacienteDentroDeSala']);
// actualizar razon de uso del paciente dentro de la ficha administrativa
    Route::post('/admin/asignaciones/actualizar/razonuso/paciente',  [AsignacionesController::class,'actualizarRazonUsoPaciente']);
// liberar sala de paciente
    Route::post('/admin/asignaciones/liberarsala/paciente',  [AsignacionesController::class,'liberarSalaPaciente']);
// informacion paciente que esta dentro de una sala y se trasladara a sala de espera de x sala
    Route::post('/admin/asignaciones/informacion/paciente/dentrosala',  [AsignacionesController::class,'informacionPacienteDentroSala']);
// trasladar paciente a nueva sala, pero se ira a sala de espera primero
    Route::post('/admin/asignaciones/traslado/paciente/reseteo',  [AsignacionesController::class,'reseteoTrasladoPacienteNuevaSala']);
// recarga por cronometro
    Route::post('/admin/asignaciones/recargando/cronometro',  [AsignacionesController::class,'recargandoVistaCronometro']);



    // --- HISTORIAL CLINICO ---
    Route::get('/admin/historial/clinico/vista/{idconsulta}', [HistorialClinicoController::class, 'indexHistorialClinico']);
    // bloque antecedentes
    Route::get('/admin/historial/bloque/antecedente/{idconsulta}', [HistorialClinicoController::class, 'bloqueHistorialAntecedente']);
    // actualizar listado de checkbox de antecedente del paciente
    Route::post('/admin/historial/antecedente/actualizacion', [HistorialClinicoController::class, 'actualizarListadoPacienteAntecedente']);

    // bloque antrop + sv
    Route::get('/admin/historial/bloque/antropsv/{idconsulta}', [HistorialClinicoController::class, 'bloqueHistorialAntropSv']);

    // vista para registar nueva antrop + sv
    Route::get('/admin/vista/nueva/antropometria/{idconsulta}', [HistorialClinicoController::class, 'vistaNuevaAntropologia']);


    // registrar formulario de antropometria
    Route::post('/admin/historial/registrar/antropometria', [HistorialClinicoController::class, 'registrarAntropometria']);

    // vista para editar o ver la antropometria
    Route::get('/admin/vista/visualizar/antropometria/{idantro}', [HistorialClinicoController::class, 'vistaVisualizarAntropologia']);



    // actualizar antropometria
    Route::post('/admin/historial/actualizar/antropometria', [HistorialClinicoController::class, 'actualizarAntropometria']);

    // editar antropometria siempre, pero se busca desde los expedientes
    Route::get('/admin/vista/visualizar/antropometria/exped/{idantro}', [HistorialClinicoController::class, 'vistaVisualizarAntropologiaExpedientes']);


    // bloque recetas
    Route::get('/admin/historial/bloque/recetas/{idconsulta}', [HistorialClinicoController::class, 'bloqueHistorialRecetas']);

    // bloque cuadro clinico
    Route::get('/admin/historial/bloque/cuadroclinico/{idconsulta}', [HistorialClinicoController::class, 'bloqueHistorialCuadroClinico']);


    Route::post('/admin/historial/borrar/antropometria', [HistorialClinicoController::class, 'borrarAntropometria']);


    // BLOQUE NOTAS
    Route::get('/admin/historial/bloque/notas/{idconsulta}', [NotasController::class, 'bloqueNotasPaciente']);
    Route::post('/admin/historial/bloque/registrar/nota', [NotasController::class, 'registrarNotaPaciente']);
    Route::post('/admin/historial/bloque/notas/borrar', [NotasController::class, 'borrarNotaPaciente']);
    Route::post('/admin/historial/bloque/notas/informacion', [NotasController::class, 'informacionNotaPaciente']);
    Route::post('/admin/historial/bloque/actualizar/nota', [NotasController::class, 'actualizarNotaPaciente']);

    // Reporte para notas paciente
    Route::get('/admin/pdf/reporte/notapaciente/{idfila}', [NotasController::class,'reporteNotaPaciente']);



    // guardar un nuevo historial clinico
    Route::post('/admin/historial/nuevo/historialclinico', [HistorialClinicoController::class, 'nuevoHistorialClinico']);

    // informacion de un cuadro clinico para editar
    Route::post('/admin/historial/informacion/historialclinico', [HistorialClinicoController::class, 'informacionHistorialClinico']);

    // actualizar un cuadro clinico
    Route::post('/admin/historial/actualizar/historialclinico', [HistorialClinicoController::class, 'actualizarHistorialClinico']);



    // vista de agregar receta
    Route::get('/admin/recetas/vista/general/{idconsulta}', [RecetasController::class, 'indexVistaNuevaReceta']);
    // listado de medicamentos por fuente
    Route::post('/admin/recetas/medicamentos/porfuente', [RecetasController::class, 'listadoMedicamentosPorFuenteFinan']);
    // registar la nueva receta al paciente
    Route::post('/admin/recetas/registro/parapaciente', [RecetasController::class, 'registroNuevaRecetaParaPaciente']);

    // vista para editar o ver la receta individual
    Route::get('/admin/recetas/vista/paraeditar/{idreceta}', [RecetasController::class, 'indexVistaEditarVerReceta']);

    // actualizar la receta si es permitido por estado
    Route::post('/admin/recetas/actualizar/parapaciente', [RecetasController::class, 'actualizarRecetaMedica']);

    // reporte de receta por idreceta
    Route::get('/admin/reporte/receta/paciente/{idreceta}', [ReportesController::class,'reporteRecetaPaciente']);

    Route::post('/admin/recetas/borrar', [ReportesController::class, 'borrarReceta']);



    // --- SALIDA MEDICAMENTO POR RECETA
    Route::get('/admin/salida/medicamento/porreceta/index', [SalidaRecetaController::class,'indexSalidaFarmaciaPorReceta'])->name('admin.salida.recetas.farmacia.index');
    Route::get('/admin/salida/medicamento/porreceta/tabla/{idestado}/{fechainicio}/{fechafin}', [SalidaRecetaController::class,'tablaSalidaFarmaciaPorReceta']);

    // informacion de receta para denegarla
    Route::post('/admin/orden/salida/informacion/paradenegar', [SalidaRecetaController::class, 'infoRecetaParaDenegar']);
    // guardar la denegacion de una receta
    Route::post('/admin/orden/salida/guardar/denegacion', [SalidaRecetaController::class, 'guardarDenegacionReceta']);
    // retornar paciente a sala de nuevo
    Route::post('/admin/paciente/retonarsala', [SalidaRecetaController::class, 'retornarPacienteSala']);
    // vista salida para procesar la receta
    Route::get('/admin/vista/procesar/recetamedica/{idreceta}', [SalidaRecetaController::class,'vistaRecetaDetalleProcesar']);
    // guardar salida de receta procesada por farmacia
    Route::post('/admin/receta/procesar/guardarsalida', [SalidaRecetaController::class, 'guardarSalidaProcesadaDeReceta']);




    Route::get('/admin/salida/medicamento/farmacia/index', [SalidaManualController::class,'indexSalidaFarmacia'])->name('admin.salida.manual.index');
    Route::post('/admin/registrar/orden/salida/medicamento', [SalidaManualController::class, 'registrarOrdenSalidaFarmacia']);
    Route::get('/admin/buscar/producto/salida/farmacia/{idproducto}', [SalidaManualController::class,'elegirProductoParaSalida']);









}); // end auth





