<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EscolaresController;
use App\Http\Controllers\IdiomasPDFController;
use App\Http\Controllers\CertificadoPDFController;
use App\Http\Controllers\ConstanciaPDFController;
use App\Http\Controllers\AlumnosController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\AcademicosController;
use App\Http\Controllers\VeranoController;
use App\Http\Controllers\PlaneacionController;
use App\Http\Controllers\DesarrolloController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(["register"=>false]);

// AQUI COMIENZA SERVICIOS ESCOLARES
Route::group(['prefix'=>'escolares','middleware'=>['auth','role:escolares']],function (){
    Route::get('/', [EscolaresController::class, 'index'])->name('inicio_escolares');
    Route::get('/alumnos/consulta', [EscolaresController::class, 'buscar']);
    Route::get('/alumnos/alta', [EscolaresController::class, 'nuevo']);
    Route::post('/alumnos/nuevo',[EscolaresController::class, 'altanuevo'])
        ->name('escolares.nuevo_alumno');
    Route::post('/alumnos/actualizar', [EscolaresController::class,'modificar_datos'])
        ->name('escolares.actualizar_alumno');
    Route::get('/periodos/alta',[EscolaresController::class, 'periodos']);
    Route::post('/periodos/nuevo',[EscolaresController::class, 'periodoalta'])
        ->name('escolares.periodo_nuevo');
    Route::get('/periodos/modifica',[EscolaresController::class, 'periodomodifica']);
    Route::post('/periodos/modificar',[EscolaresController::class, 'periodomodificar'])
        ->name('escolares.periodo_mod1');
    Route::post('/periodo/modificado',[EscolaresController::class, 'periodoupdate'])
        ->name('escolares.periodo_upd');
    Route::get('/actas',[EscolaresController::class, 'periodoactas1']);
    Route::post('/periodo/actas2',[EscolaresController::class, 'periodoactas2'])
        ->name('escolares.actas2');
    Route::post('/periodo/actas3',[EscolaresController::class, 'periodoactas3'])
        ->name('escolares.actas3');
    Route::get('/acta/modificar/{periodo}/{rfc}/{materia}/{grupo}',[EscolaresController::class, 'modificaracta']);
    Route::get('/acta/imprimir/{periodo}/{rfc}/{materia}/{grupo}',[EscolaresController::class, 'imprimiracta']);
    Route::post('/periodo/actasupdate',[EscolaresController::class, 'actasupdate'])
        ->name('escolares.actas_upd');
    Route::post('/buscar',[EscolaresController::class, 'busqueda'])
        ->name('escolares.buscar');
    Route::post('/acciones',[EscolaresController::class, 'accion'])
        ->name('escolares.accion');
    Route::post('/accionesk',[EscolaresController::class, 'accionk'])
        ->name('escolares.accion_kardex');
    Route::post('/accionesk_alta',[EscolaresController::class, 'accionkalta'])
        ->name('escolares.accion_kardex_alta');
    Route::post('/periodo_k',[EscolaresController::class, 'accionkperiodo'])
        ->name('escolares.accion_kardex_modificar1');
    Route::get('/modificar/{periodo}/{control}/{materia}',[EscolaresController::class, 'modificarkardex']);
    Route::get('/eliminar/{periodo}/{control}/{materia}',[EscolaresController::class, 'eliminarkardex']);
    Route::post('/imprimir_boleta',[EscolaresController::class, 'imprimirboleta'])
        ->name('escolares.imprimir boleta');
    Route::post('/actualizar/kardex',[EscolaresController::class, 'kardexupdate'])
        ->name('escolares.accion_actualiza_kardex');
    Route::post('/actualizar/estatus',[EscolaresController::class, 'estatusupdate'])
        ->name('escolares.accion_actualiza_estatus');
    Route::post('/actualizar/especialidad',[EscolaresController::class, 'especialidadupdate'])
        ->name('escolares.accion_actualiza_especialidad');
    Route::post('/actualizar/carrera',[EscolaresController::class, 'carreraupdate'])
        ->name('escolares.accion_actualiza_carrera');
    Route::post('/eliminar/',[EscolaresController::class, 'alumnodelete'])
        ->name('escolares.accion_borrar');
    Route::post('/baja/',[EscolaresController::class, 'alumnobajatemp'])
        ->name('escolares.accion_bajatemp');
    Route::post('/nss/',[EscolaresController::class, 'alumnonss'])
        ->name('escolares.nss');
    Route::get('/reinscripcion',[EscolaresController::class, 'reinscripcion']);
    Route::post('/reinscripcion/acciones',[EscolaresController::class, 'accion_re'])
        ->name('escolares.accion-reinscripcion');
    Route::post('/reinscripcion/alta_fechas',[EscolaresController::class, 'altaf_re'])
        ->name('escolares.fechas-reinscripcion');
    Route::get('/estadistica/prepoblacion',[EscolaresController::class, 'prepoblacion']);
    Route::post('/estadistica/poblacion',[EscolaresController::class, 'poblacion'])
        ->name('escolares.poblacion');
    Route::get('/estadistica/{periodo}/{carrera}/{reticula}',[EscolaresController::class, 'pobxcarrera'])
        ->name('escolares.est_carrera');
    Route::get('/contrasena',[EscolaresController::class, 'contrasenia']);
    Route::post('/ccontrasena',[EscolaresController::class, 'ccontrasenia'])
        ->name('escolares.contra');
    Route::post('/constancia',[ConstanciaPDFController::class,'crearPDF'])
        ->name('escolares.constancia');
    Route::get('/carreras/alta',[EscolaresController::class, 'carrerasalta']);
    Route::post('/carreras/alta_procesa',[EscolaresController::class, 'carreranueva'])
        ->name('escolares.carrera_alta');
    Route::get('/carreras/especialidades',[EscolaresController::class, 'especialidadesalta']);
    Route::get('/carreras/materias',[EscolaresController::class, 'materianueva']);
    Route::post('/especialidad/alta',[EscolaresController::class, 'especialidadnueva'])
        ->name('escolares.especialidad_alta');
    Route::post('/materias/accion',[EscolaresController::class, 'materiasacciones'])
        ->name('escolares.materias_acciones');
    Route::post('/materias/nueva',[EscolaresController::class, 'materiaalta'])
        ->name('escolares.materia_nueva');
    Route::post('/reticulas/vista',[EscolaresController::class, 'vistareticula'])
        ->name('escolares.vista_reticula');
    Route::post('/certificado',[EscolaresController::class, 'certificado'])
        ->name('escolares.certificado');
    Route::post('/idiomas',[IdiomasPDFController::class,'crearPDF'])
        ->name('escolares.idiomas');
    Route::post('/imprimir_certificado',[CertificadoPDFController::class,'crearPDF'])
        ->name('escolares.certificado_pdf');
    Route::get('/actas/registro',[EscolaresController::class, 'periodoactas_m1']);
    Route::post('/periodo/registro2',[EscolaresController::class, 'periodoactas_m2'])
        ->name('escolares.registro2');
    Route::post('/periodo/registro3',[EscolaresController::class, 'periodoactas_m3'])
        ->name('escolares.registro3');
    Route::post('/periodo/registro4',[EscolaresController::class, 'periodoactas_m4'])
        ->name('escolares.registro4');
    Route::get('/actas/mantenimiento',[EscolaresController::class, 'actas_mantenimiento']);
    Route::post('/actas/consulta_estatus',[EscolaresController::class, 'actas_estatus'])
        ->name('escolares.actas_estatus');
    Route::get('/cierre',[EscolaresController::class, 'cierre']);
    Route::post('/cerrar_periodo',[EscolaresController::class, 'cierre_accion'])
        ->name('escolares.cierre_semestre');
    Route::get('/idiomas/liberacion',[EscolaresController::class, 'idiomas_lib1']);
    Route::post('/idiomas/liberar',[EscolaresController::class, 'idiomas_lib2'])
        ->name('escolares.liberar_idioma');
    Route::post('/idiomas/liberar2',[EscolaresController::class, 'idiomas_lib3'])
        ->name('escolares.liberar_idioma2');
    Route::get('/idiomas/impresion',[EscolaresController::class, 'idiomas_impre']);
    Route::post('/idiomas/imprimir2',[EscolaresController::class, 'idiomas_impre2'])
        ->name('escolares.imprimir_idioma');
    Route::get('/idiomas/consulta',[EscolaresController::class, 'idiomas_consulta']);
    Route::post('/idiomas/consultar',[EscolaresController::class, 'idiomas_consulta2'])
        ->name('escolares.cursos_idiomas');
});
//AQUI TERMINA SERVICIOS ESCOLARES

//AQUI COMIENZA ALUMNO
Route::group(['prefix'=>'estudiante','middleware'=>['auth','role:alumno']],function (){
    Route::get('/', [AlumnosController::class, 'index'])->name('inicio_alumno');
    Route::get('/kardex',[AlumnosController::class, 'ver_kardex']);
    Route::get('/horario',[AlumnosController::class, 'horario']);
    Route::get('/eval',[AlumnosController::class, 'evaluacion']);
    Route::post('/evaluacion',[AlumnosController::class, 'evaluar'])->name('eval_doc');
    Route::post('/eval_doc',[AlumnosController::class, 'evaluaciondoc'])->name('eval_docente');
    Route::get('/boleta',[AlumnosController::class, 'boleta']);
    Route::post('/boleta2',[AlumnosController::class, 'verboleta'])->name('alumno_boleta');
    Route::get('/reticula',[AlumnosController::class, 'verreticula']);
    Route::get('/reinscripcion',[AlumnosController::class, 'reinscripcion']);
    Route::get('/reinscripcion/{materia}/{tipocur}',[AlumnosController::class, 'seleccion_materia']);
    Route::get('/calificaciones',[AlumnosController::class, 'vercalificaciones']);
    Route::post('/seleccion/',[AlumnosController::class, 'reinscribir'])->name('alumno.seleccion');
    Route::get('/eliminar',[AlumnosController::class, 'bajam']);
    Route::post('/seleccion/baja',[AlumnosController::class, 'baja_materia'])->name('alumno.baja_m');
    Route::post('/accionesk',[AlumnosController::class, 'accionkardex'])->name('alumno.accion_kardex');
});
//AQUI TERMINA ALUMNO

//AQUI COMIENZA DOCENTE
Route::group(['prefix'=>'personal','middleware'=>['auth','role:docente']],function (){
    Route::get('/', [PersonalController::class, 'index'])->name('inicio_personal');
    Route::get('/semestre',[PersonalController::class, 'encurso']);
    Route::get('/semestre/listas/{materia}/{gpo}',[PersonalController::class, 'lista']);
    Route::get('/semestre/excel/{materia}/{gpo}',[PersonalController::class, 'excel']);
    Route::get('/semestre/evaluar/{materia}/{gpo}',[PersonalController::class, 'evaluar']);
    Route::post('/semestre/calificaciones',[PersonalController::class, 'calificar'])
        ->name('personal_cal1');
    Route::get('/semestre/acta/{materia}/{gpo}',[PersonalController::class, 'acta']);
    Route::get('/contrasena',[PersonalController::class, 'contrasenia']);
    Route::post('/ccontrasena',[PersonalController::class, 'ccontrasenia'])
        ->name('personal_contra');
    Route::get('/residencias',[PersonalController::class, 'residencias1']);
    Route::post('/eval/residencias',[PersonalController::class, 'residencias2'])
        ->name('personal_residencias1');
    Route::get('/residencias/evaluar/{periodo}/{materia}/{gpo}/{control}',[PersonalController::class, 'residenciaevaluar']);
    Route::post('/residencias/calificar',[PersonalController::class, 'residenciascalifica'])
        ->name('personal_residencias2');
    Route::get('/residencias/acta/{periodo}/{materia}/{gpo}',[PersonalController::class, 'actaresidencia']);
});
//AQUI TERMINA DOCENTE

//AQUI COMIENZA DIVISION
Route::group(['prefix'=>'dep','middleware'=>['auth','role:division']],function (){
    Route::get('/', [DivisionController::class, 'index'])
        ->name('inicio_division');
    Route::get('/alta/grupo',[DivisionController::class, 'altagrupo']);
    Route::post('/alta/materias',[DivisionController::class, 'listado2'])
        ->name('dep_lista2');
    Route::get('/grupos/alta/{periodo}/{materia}/{carrera}/{reticula}',[DivisionController::class, 'creargrupo1'])
        ->name('dep_alta_grupo');
    Route::post('/alta/grupo',[DivisionController::class, 'creargrupo2'])
        ->name('dep_grupo_alta');
    Route::get('/alta/paralelo',[DivisionController::class, 'paralelo1']);
    Route::post('/alta/paralela2',[DivisionController::class, 'paralelo2'])
        ->name('dep_paralelo2');
    Route::post('/alta/paralela3',[DivisionController::class, 'paralelo3'])
        ->name('dep_paralelo3');
    Route::get('/existentes',[DivisionController::class, 'existentes']);
    Route::post('/listado/',[DivisionController::class, 'listado'])
        ->name('dep_lista');
    Route::post('/listado2/',[DivisionController::class, 'listado2'])
        ->name('dep_infogpo');
    Route::get('/grupos/info/{periodo}/{materia}/{gpo}',[DivisionController::class, 'info'])
        ->name('dep_info');
    Route::post('/acciones/',[DivisionController::class, 'acciones'])
        ->name('dep_acciones');
    Route::post('/altaa/',[DivisionController::class, 'altacontrol'])
        ->name('dep_altaa');
    Route::delete('/bajaa/',[DivisionController::class, 'bajacontrol'])
        ->name('dep_bajaa');
    Route::post('/grupos/modificar/horario',[DivisionController::class, 'updatehorario'])
        ->name('dep_grupo_modifica');
    Route::post('/grupos/capacidad',[DivisionController::class, 'capgrupo'])
        ->name('dep_cap_grupo');
    Route::get('/alumnos/consulta',[DivisionController::class, 'buscar']);
    Route::post('/alumno/buscar',[DivisionController::class, 'busqueda'])
        ->name('dep.buscar');
    Route::post('/alumno/datos',[DivisionController::class, 'accion2'])
        ->name('dep.accion2');
    Route::get('/estadistica/prepoblacion',[DivisionController::class, 'prepoblacion']);
    Route::post('/estadistica/poblacion',[DivisionController::class, 'poblacion'])
        ->name('dep_poblacion');
    Route::get('/estadistica/{periodo}/{carrera}/{reticula}',[DivisionController::class, 'pobxcarrera'])
        ->name('dep_est_carrera');
    Route::get('/estadistica/aulas',[DivisionController::class, 'pobxaulas']);
    Route::post('/estadistico/aula2',[DivisionController::class, 'pobxaulas2'])
        ->name('dep_aula');
    Route::get('/contrasena',[DivisionController::class, 'contrasenia']);
    Route::post('/ccontrasena',[DivisionController::class, 'ccontrasenia'])
        ->name('division_contra');
    Route::get('/alumnos/psemestre',[DivisionController::class, 'psemestre']);
    Route::post('/alumnos/cambio_primer',[DivisionController::class, 'psemestre2'])
        ->name('division_cambio_primer');
    Route::post('/alumnos/cambio_primer2',[DivisionController::class, 'psemestrecambio'])
        ->name('division_cambio_primer2');
    //En desuso
    Route::post('/altad/',[DivisionController::class, 'altadocente'])
        ->name('dep_altad');
});
//AQUI TERMINA DIVISION

//AQUI COMIENZA ACADEMICOS
Route::group(['prefix'=>'acad','middleware'=>['auth','role:acad']],function (){
    Route::get('/', [AcademicosController::class, 'index'])
        ->name('inicio_academicos');
    Route::get('/existentes',[AcademicosController::class, 'existentes']);
    Route::post('/listado/',[AcademicosController::class, 'listado'])
        ->name('acad_lista');
    Route::get('/grupos/info/{periodo}/{materia}/{gpo}',[AcademicosController::class, 'info'])
        ->name('acad_info');
    Route::post('/acciones/',[AcademicosController::class, 'acciones'])
        ->name('acad_acciones');
    Route::post('/altad/',[AcademicosController::class, 'altadocente'])
        ->name('acad_altad');
    Route::get('/estadistica/prepoblacion',[AcademicosController::class, 'prepoblacion']);
    Route::post('/estadistica/poblacion',[AcademicosController::class, 'poblacion'])
        ->name('acad_poblacion');
    Route::get('/estadistica/{periodo}/{carrera}/{reticula}',[AcademicosController::class, 'pobxcarrera'])
        ->name('acad_est_carrera');
    Route::get('/estadistica/aulas',[AcademicosController::class, 'pobxaulas']);
    Route::post('/estadistico/aula2',[AcademicosController::class, 'pobxaulas2'])
        ->name('acad_aula');
    Route::get('/estadistica/predocentes',[AcademicosController::class, 'predocentes']);
    Route::post('/estadistico/personal',[AcademicosController::class, 'docente'])
        ->name('acad_personal');
    Route::get('/horarios',[AcademicosController::class, 'otroshorarios']);
    Route::post('/horarios/accion',[AcademicosController::class, 'otroshorariosaccion'])
        ->name('acad_otrosh');
    Route::post('/horarios/alta_admin',[AcademicosController::class, 'procesaadmvoalta'])
        ->name('acad_altaadmin');
    Route::get('/modificar/admvo/{periodo}/{personal}/{numero}',[AcademicosController::class, 'modificaadmvo'])
        ->name('acad_modhadmin');
    Route::get('/eliminar/admvo/{periodo}/{personal}/{numero}',[AcademicosController::class, 'eliminaadmvo'])
        ->name('acad_delhadmin');
    Route::post('/actualizar/hadmvo',[AcademicosController::class, 'procesoadmvoupdate'])
        ->name('acad_modadmin');
    Route::get('/modificar/apoyo/{periodo}/{personal}/{numero}',[AcademicosController::class, 'modificaapoyo'])
        ->name('acad_modhapoyo');
    Route::get('/eliminar/apoyo/{periodo}/{personal}/{numero}',[AcademicosController::class, 'eliminaapoyo'])
        ->name('acad_delhapoyo');
    Route::post('/actualizar/hapoyo',[AcademicosController::class, 'procesoapoyoupdate'])
        ->name('acad_modapoyo');
    Route::post('/horarios/alta_apoyo',[AcademicosController::class, 'procesaapoyoalta'])
        ->name('acad_altaapoyo');
    Route::post('/horarios/alta_obs',[AcademicosController::class, 'altaobservacion'])
        ->name('acad_altaobs');
    Route::get('/modificar/obs/{periodo}/{personal}',[AcademicosController::class, 'modificaobservaciones'])
        ->name('acad_modobs');
    Route::post('/actualizar/observaciones',[AcademicosController::class, 'observacionesupdate'])
        ->name('acad_modobservaciones');
    Route::get('/eliminar/obs/{periodo}/{personal}',[AcademicosController::class, 'eliminaobservaciones'])
        ->name('acad_deloobs');
    Route::get('/contrasena',[AcademicosController::class, 'contrasenia']);
    Route::post('/ccontrasena',[AcademicosController::class, 'ccontrasenia'])
        ->name('acad_contra');
    Route::post('/impresion/horario','HorarioPDFController@crearPDF')
        ->name('horario_ind');
    Route::get('/modificar',[AcademicosController::class, 'cambiarcontra']);
    Route::post('/modificar_contra',[AcademicosController::class, 'cambiarcontra2'])
        ->name('acad_cambiar_contra_doc');
    Route::post('/modificar_contra2',[AcademicosController::class, 'cambiarcontra3'])
        ->name('acad_cambiar_contra_doc2');
    Route::get('/reset',[AcademicosController::class, 'actareset']);
    Route::post('/reset_acta',[AcademicosController::class, 'actareset2'])
        ->name('acad_reset2');
    Route::post('/reset_acta2',[AcademicosController::class, 'actareset3'])
        ->name('acad_reset3');
});
//AQUI TERMINA ACADEMICOS

//AQUI COMIENZA COORD VERANO
Route::group(['prefix'=>'verano','middleware'=>['auth','role:verano']],function (){
    Route::get('/', [VeranoController::class, 'index'])
        ->name('inicio_verano');
    Route::get('/existentes',[VeranoController::class, 'existentes']);
    Route::get('/alta/paralelo',[VeranoController::class, 'paralelo1']);
    Route::post('/listado/',[VeranoController::class, 'listado'])
        ->name('verano_lista');
    Route::post('/listado2/',[VeranoController::class, 'listado2'])
        ->name('verano_infogpo');
    Route::get('/grupos/info/{materia}/{gpo}',[VeranoController::class, 'info'])
        ->name('verano_info');
    Route::post('/acciones/',[VeranoController::class, 'acciones'])
        ->name('verano_acciones');
    Route::post('/altaa/',[VeranoController::class, 'altacontrol'])
        ->name('verano_altaa');
    Route::delete('/bajaa/',[VeranoController::class, 'bajacontrol'])
        ->name('verano_bajaa');
    Route::post('/altad/',[VeranoController::class, 'altadocente'])
        ->name('verano_altad');
    Route::get('/alta/grupo',[VeranoController::class, 'altagrupo']);
    Route::post('/alta/materias',[VeranoController::class, 'listado2'])
        ->name('verano_lista2');
    Route::get('/grupos/alta/{materia}/{carrera}/{reticula}',[VeranoController::class, 'creargrupo1'])
        ->name('verano_alta_grupo');
    Route::post('/alta/grupo',[VeranoController::class, 'creargrupo2'])
        ->name('verano_grupo_alta');
    Route::post('/alta/paralela2',[VeranoController::class, 'paralelo2'])
        ->name('verano_paralelo2');
    Route::post('/alta/paralela3',[VeranoController::class, 'paralelo3'])
        ->name('verano_paralelo3');
    Route::get('/modificar/grupo',[VeranoController::class, 'modificar1']);
    Route::post('/modificar/grupo2',[VeranoController::class, 'modificar2'])
        ->name('verano_modificar2');
    Route::get('/grupos/modificar/{materia}/{gpo}',[VeranoController::class, 'modificar3'])
        ->name('verano_modificar3');
    Route::post('/grupos/modificar/horario',[VeranoController::class, 'updatehorario'])
        ->name('verano_grupo_modifica');
    Route::get('/alumnos/consulta',[VeranoController::class, 'buscar']);
    Route::post('/alumno/buscar',[VeranoController::class, 'busqueda'])
        ->name('verano.buscar');
    Route::post('/alumno/datos',[VeranoController::class, 'accion2'])
        ->name('verano.accion2');
    Route::get('/estadistica/poblacion',[VeranoController::class, 'poblacion']);
    Route::get('/estadistica/{carrera}/{reticula}',[VeranoController::class, 'pobxcarrera'])
        ->name('verano_est_carrera');
    Route::get('/estadistica/aulas',[VeranoController::class, 'pobxaulas']);
    Route::post('/estadistico/aula2',[VeranoController::class, 'pobxaulas2'])
        ->name('verano_aula');
});
//AQUI TERMINA COORD VERANO

//AQUI COMIENZA PLANEACION
Route::group(['prefix'=>'planeacion','middleware'=>['auth','role:planeacion']],function (){
    Route::get('/', [PlaneacionController::class, 'index'])
        ->name('inicio_planeacion');
    Route::get('/estadistica/prepoblacion',[PlaneacionController::class, 'prepoblacion']);
    Route::get('/estadistica/preedades',[PlaneacionController::class, 'preedades']);
    Route::get('/estadistica/preedadesc',[PlaneacionController::class, 'preedadesc']);
    Route::get('/estadistica/preedadesedo',[PlaneacionController::class, 'preedadesedo']);
    Route::post('/estadistica/poblacion',[PlaneacionController::class, 'poblacion'])
        ->name('planeacion.poblacion');
    Route::post('/estadistica/edades',[PlaneacionController::class, 'edades'])
        ->name('planeacion.edades');
    Route::post('/estadistica/edades_carrera',[PlaneacionController::class, 'edadesc'])
        ->name('planeacion.edadesc');
    Route::post('/estadistica/edades_estado',[PlaneacionController::class, 'edadesedo'])
        ->name('planeacion.edadesedo');
    Route::get('/estadistica/{periodo}/{carrera}/{reticula}',[PlaneacionController::class, 'pobxcarrera'])
        ->name('planeacion.est_carrera');
    Route::get('/estadistica/preegreso',[PlaneacionController::class, 'preegreso']);
    Route::get('/estadistica/preegreso2',[PlaneacionController::class, 'preegreso2']);
    Route::post('/estadistica/consulta_egreso',[PlaneacionController::class, 'egreso'])
        ->name('planeacion.egreso');
    Route::post('/estadistica/consulta_egreso2',[PlaneacionController::class, 'egreso2'])
        ->name('planeacion.egreso_completo');
    Route::get('/carreras/materias',[PlaneacionController::class, 'materianueva']);
    Route::post('/materias/accion',[PlaneacionController::class, 'materiasacciones'])
        ->name('planeacion.materias_acciones');
    Route::post('/reticulas/vista',[PlaneacionController::class, 'vistareticula'])
        ->name('planeacion.vista_reticula');
    Route::get('/contrasena',[PlaneacionController::class, 'contrasenia']);
    Route::post('/ccontrasena',[PlaneacionController::class, 'ccontrasenia'])
        ->name('planeacion.contra');
});
//AQUI TERMINA PLANEACION

//AQUI COMIENZA DESARROLLO ACADEMICO
Route::group(['prefix'=>'desacad','middleware'=>['auth','role:desacad']],function (){
    Route::get('/', [DesarrolloController::class, 'index'])
        ->name('inicio_desarrollo');
    Route::get('/fichas/inicio',[DesarrolloController::class, 'fichas_inicio']);
    Route::post('/fichas/parametros1',[DesarrolloController::class, 'fichas_inicio_parametros'])
        ->name('desacad.parametros_fichas');
    Route::post('/fichas/parametros2',[DesarrolloController::class, 'fichas_inicio_aulas'])
        ->name('desacad.parametros_aulas');
    Route::get('/fichas/carreras',[DesarrolloController::class, 'fichas_carreras']);
    Route::post('/fichas/carreras1',[DesarrolloController::class, 'fichas_carreras_actualizar'])
        ->name('desacad.carreras_ofertar');
    Route::get('/fichas/aulas',[DesarrolloController::class, 'fichas_aulas_mostrar'])->name('fichas_carreras');
    Route::post('/fichas/aulas1',[DesarrolloController::class, 'fichas_aulas_actualizar'])
        ->name('desacad.aulas_actualizar');
    Route::post('/fichas/aulas2',[DesarrolloController::class, 'fichas_aulas_actualizar2'])
        ->name('desacad.aulas_actualizar2');
    Route::get('/fichas/aula/editar',[DesarrolloController::class, 'fichas_aulas_editar']);

});
//AQUI TERMINA DESARROLLO ACADEMICO
