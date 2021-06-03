<?php

namespace App\Http\Controllers;

use App\Alumnos;
use App\AlumnosGenerales;
use App\HistoriaAlumno;
use App\Personal;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;

class EscolaresController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('escolares.inicio');
    }

    public function periodo()
    {
        $periodo_actual = Db::Select('select periodo from pac_periodo_actual()');
        return $periodo_actual;
    }

    public function inscritos($periodo)
    {
        $data = DB::select("select * from pac_poblacion('$periodo')");
        return $data;
    }

    public function semreal($periodo_ingreso, $periodo)
    {
        $anio_actual = substr($periodo, 0, 4);
        $anio_ingresa = substr($periodo_ingreso, 0, 4);
        $tipo_ingreso = substr($periodo_ingreso, -1);
        $per = substr($periodo, -1);
        if ($per == "3") {
            $semestre = ($tipo_ingreso == '3') ? (2 * ($anio_actual - $anio_ingresa) + 1) : (2 * ($anio_actual - $anio_ingresa) + 2);
        } else {
            $semestre = ($tipo_ingreso == '3') ? (2 * ($anio_actual - $anio_ingresa)) : (2 * ($anio_actual - $anio_ingresa) + 1);
        }
        return $semestre;
    }

    public function kardex($control)
    {
        //Primero, busco los periodos que ha tenido
        $inscrito_en = DB::table('historia_alumno')
            ->select('periodo')
            ->where('no_de_control', $control)
            ->distinct()
            ->get();
        $calificaciones = array();
        foreach ($inscrito_en as $cuando) {
            //Ahora, materias y calificaciones
            $data = DB::select("select * from pac_calificaciones('$control','$cuando->periodo')");
            $calificaciones[$cuando->periodo] = $data;
        }
        //dd($calificaciones);
        return $calificaciones;
    }

    public function historial($control)
    {
        $data = DB::select("select * from pac_historia_escolar_alumno('$control')");
        return $data;
    }

    public function reticula($control)
    {
        //Primero, busco los periodos que ha tenido
        $data = DB::select("select * from pac_reticulaalumno('$control')");
        return $data;
    }

    public function boleta($control, $periodo)
    {
        //Primero, busco los periodos que ha tenido
        $data = DB::select("select * from pac_calificaciones('$control','$periodo')");
        return $data;
    }

    public function sin_evaluar($periodo)
    {
        $data = DB::select("select * from pac_materias_faltan('$periodo')");
        return $data;
    }

    public function evaluadas($periodo)
    {
        $data = DB::select("select * from pac_materias_calificadas('$periodo')");
        return $data;
    }

    public function actas_faltantes($periodo)
    {
        $data = DB::select("select * from pac_actas_faltan('$periodo')");
        return $data;
    }

    public function nperiodo($control)
    {
        //Primero, busco los periodos que ha tenido
        $inscrito_en = DB::table('historia_alumno')
            ->select('periodo')
            ->where('no_de_control', $control)
            ->distinct()
            ->get();
        $nombres = array();
        foreach ($inscrito_en as $cuando) {
            //Ahora, nombres
            $data = DB::table('periodos_escolares')->where('periodo', $cuando->periodo)->get();
            $nombres[$cuando->periodo] = $data;
        }
        return $nombres;
    }

    public function buscar()
    {
        return view('escolares.busqueda');
    }
    public function actualizar_kardex($periodo,$quien){
        DB::select("select * from pap_agrega_calif_a_histo('$periodo','$quien')");
        return 1;
    }
    public function calcular_promedios($periodo,$ncontrol){
        DB::select("select * from pap_promedios_alumno('$ncontrol','$periodo')");
        return 1;
    }
    public function avisos_reinscripcion($periodo_cierre,$periodo_siguiente){
        DB::select("select * from pap_avisos_reinscripcion('$periodo_cierre','$periodo_siguiente')");
        return 1;
    }
    public function actualizar_semestre($periodo){
        DB::select("select * from pap_semestre_alumno('$periodo')");
        return 1;
    }
    public function curso_especial($periodo){
        DB::select("select * from pap_curso_especial('$periodo')");
        return 1;
    }
    public function consulta_idiomas($periodo,$idioma)
    {
        $data = DB::select("select * from pac_idiomas_consulta('$periodo',$idioma)");
        return $data;
    }
    public function nuevo()
    {
        $estados = DB::table('entidades_federativas')->get();
        $per = $this->periodo();
        $periodo = $per[0]->periodo;
        if (DB::table('alumnos')->where('periodo_ingreso_it', $periodo)->where('nivel_escolar', 'L')
                ->where('tipo_ingreso', '1')->count() > 0) {
            $ultimo = DB::table('alumnos')->where('periodo_ingreso_it', $periodo)
                ->where('nivel_escolar', 'L')
                ->where('tipo_ingreso', '1')->max('no_de_control');
            $nperiodo = DB::table('periodos_escolares')->where('periodo', $periodo)->first();
            $mensaje = "El último número de control asignado en " . $nperiodo->identificacion_corta . " fue " . $ultimo;
        } else {
            $last = substr($periodo, -1);
            $anio = substr($periodo, 0, 4);
            if ($last == 1) {
                $anio_ant = $anio - 1;
                $per_ult = $anio_ant . "3";
            } else {
                $per_ult = $anio . "1";
            }
            $ultimo = DB::table('alumnos')->where('periodo_ingreso_it', $per_ult)
                ->where('nivel_escolar', 'L')
                ->where('tipo_ingreso', '1')->max('no_de_control');
            $nperiodo = DB::table('periodos_escolares')->where('periodo', $per_ult)->first();
            $mensaje = "El último número de control asignado en " . $nperiodo->identificacion_corta . " fue " . $ultimo;
        }
        $periodos = DB::table('periodos_escolares')
            ->orderBy('periodo', 'desc')
            ->get();
        $carreras = DB::table('carreras')->orderBy('nombre_reducido')->get();
        $planes = DB::table('planes_de_estudio')->get();
        $tipos_ingreso = DB::table('tipos_ingreso')->get();
        return view('escolares.nuevo')->with(compact('estados', 'periodo', 'mensaje', 'periodos', 'carreras', 'planes','tipos_ingreso'));
    }

    public function altanuevo(Request $request)
    {
        $data = request()->validate([
            'control' => 'required',
            'apmat' => 'required',
            'nombre' => 'required',
            'semestre' => 'required',
            'curp' => 'required',
            'fnac' => 'required'
        ], [
            'control.required' => 'Debe indicar el numero de control',
            'apmat.required' => 'Debe escribir el apellido materno',
            'nombre.required' => 'Debe escribir el nombre',
            'semestre.required' => 'Debe indicar el semestre que se encuentra actualmente',
            'curp.required' => 'Debe escribir el CURP',
            'fnac.required' => 'Debe escribir la fecha de nacimiento'
        ]);
        $control = $request->get('control');
        if (DB::table('alumnos')->where('no_de_control', $control)->count() > 0) {
            $mensaje = "El numero de control ya existe en la base de datos";
            return view('escolares.no')->with(compact('mensaje'));
        } else {
            $appat = $request->get('appat');
            $apmat = $request->get('apmat');
            $nombre = $request->get('nombre');
            $carr = $request->get('carrera');
            $datos = explode("_", $carr);
            $carrera = $datos[0];
            $reticula = $datos[1];
            $nivel = $datos[2];
            $semestre = $request->get('semestre');
            $plan = $request->get('plan');
            $ingreso = $request->get('ingreso');
            $nss = $request->get('nss');
            $curp = $request->get('curp');
            $nip = rand(1000, 9999);
            $lnac = $request->get('lnac');
            $ciudad = $request->get('ciudad');
            $fnac = $request->get('fnac');
            $sexo = $request->get('sexo');
            $ecivil = $request->get('ecivil');
            $calle = $request->get('calle');
            $colonia = $request->get('colonia');
            $cp = $request->get('cp');
            $telcel = $request->get('telcel');
            $correo = $request->get('correo');
            $proc = $request->get('proc');
            $rev = $request->get('rev');
            $tipo = $request->get('tipo');
            $quien = Auth::user()->email;
            DB::table('alumnos')->insert([
                'no_de_control' => $control,
                'carrera' => $carrera,
                'reticula' => $reticula,
                'especialidad' => null,
                'nivel_escolar' => $nivel,
                'semestre' => $semestre,
                'estatus_alumno' => 'ACT',
                'plan_de_estudios' => $plan,
                'apellido_paterno' => $appat,
                'apellido_materno' => $apmat,
                'nombre_alumno' => $nombre,
                'curp_alumno' => $curp,
                'fecha_nacimiento' => $fnac,
                'sexo' => $sexo,
                'estado_civil' => $ecivil,
                'tipo_ingreso' => $tipo,
                'periodo_ingreso_it' => $ingreso,
                'ultimo_periodo_inscrito' => null,
                'promedio_periodo_anterior' => null,
                'promedio_aritmetico_acumulado' => null,
                'creditos_aprobados' => null,
                'creditos_cursados' => null,
                'promedio_final_alcanzado' => null,
                'escuela_procedencia' => $proc,
                'entidad_procedencia' => $lnac,
                'ciudad_procedencia' => $ciudad,
                'correo_electronico' => $correo,
                'periodos_revalidacion' => $rev,
                'becado_por' => null,
                'nip' => $nip,
                'usuario' => $quien,
                'fecha_actualizacion' => null,
                'fecha_titulacion' => null,
                'opcion_titulacion' => null,
                'periodo_titulacion' => null,
                'registro_patronal' => null,
                'digito_registro_patronal' => null,
                'nss' => $nss,
                'created_at' => Carbon::now(),
                'updated_at' => null
            ]);
            DB::table('alumnos_generales')->insert([
                'no_de_control' => $control,
                'domicilio_calle' => $calle,
                'domicilio_colonia' => $colonia,
                'codigo_postal' => $cp,
                'telefono' => $telcel,
                'facebook' => null,
                'created_at' => Carbon::now(),
                'updated_at' => null
            ]);
            $ncarrera = DB::table('carreras')->where('carrera', $carrera)->where('reticula', $reticula)
                ->first();
            $data = [
                'appat' => $appat,
                'apmat' => $apmat,
                'nombre' => $nombre,
                'control' => $control,
                'ncarrera' => $ncarrera,
                'nip' => $nip
            ];
            $pdf = PDF::loadView('escolares.pdf_nuevo', $data)
                ->setPaper('Letter');
            return $pdf->download('alta.pdf');
        }
    }

    public function periodos()
    {
        $yr = date('Y');
        return view('escolares.periodos')->with(compact('yr'));
    }

    public function periodoalta(Request $request)
    {
        $data = request()->validate([
            'finicio' => 'required',
            'ftermino' => 'required',
            'finicio_vac' => 'required',
            'ftermino_vac' => 'required',
            'finicio_cap' => 'required',
            'ftermino_cap' => 'required',
            'finicio_est' => 'required',
            'ftermino_est' => 'required'
        ], [
            'finicio.required' => 'Debe indicar la fecha de inicio del semestre',
            'ftermino.required' => 'Debe escribir la fecha de término del semestre',
            'finicio_vac.required' => 'Debe indicar la fecha de inicio de vacaciones para el semestre',
            'ftermino_vac.required' => 'Debe escribir la fecha de término de vacaciones para el semestre',
            'finicio_cap.required' => 'Debe indicar la fecha de inicio de captura docente para el semestre',
            'ftermino_cap.required' => 'Debe escribir la fecha de término de captura docente para el semestre',
            'finicio_est.required' => 'Debe indicar la fecha de inicio de selección de materias del estudiante para el semestre',
            'ftermino_est.required' => 'Debe escribir la fecha de término de selección de materias del estudiante para el semestre'
        ]);
        $anio = $request->get('anio');
        $tper = $request->get('tper');
        $periodo = $anio . $tper;
        if (DB::table('periodos_escolares')->where('periodo', $periodo)->count() > 0) {
            $mensaje = "No se puede crear el período porque ya existe en la base de datos";
            return view('escolares.no')->with(compact('mensaje'));
        } else {
            switch ($tper) {
                case 1:
                {
                    $id_largo = "ENERO-JUNIO/" . $anio;
                    $id_corto = "ENE-JUN/" . $anio;
                    break;
                }
                case 2:
                {
                    $id_largo = "VERANO/" . $anio;
                    $id_corto = "Verano/" . $anio;
                    break;
                }
                case 3:
                {
                    $id_largo = "AGOSTO-DICIEMBRE/" . $anio;
                    $id_corto = "AGO-DIC/" . $anio;
                    break;
                }
            }
            $finicio = $request->get('finicio');
            $ftermino = $request->get('ftermino');
            $finicio_ss1 = $request->get('finicio_ss');
            $ftermino_ss1 = $request->get('ftermino_ss');
            $finicio_ss = empty($finicio_ss1) ? null : $finicio_ss1;
            $ftermino_ss = empty($ftermino_ss1) ? null : $ftermino_ss1;
            $finicio_vac = $request->get('finicio_vac');
            $ftermino_vac = $request->get('ftermino_vac');
            $finicio_cap = $request->get('finicio_cap');
            $ftermino_cap = $request->get('ftermino_cap');
            $finicio_est = $request->get('finicio_est');
            $ftermino_est = $request->get('ftermino_est');
            DB::table('periodos_escolares')->insert([
                'periodo' => $periodo,
                'identificacion_larga' => $id_largo,
                'identificacion_corta' => $id_corto,
                'fecha_inicio' => $finicio,
                'fecha_termino' => $ftermino,
                'inicio_vacacional_ss' => $finicio_ss,
                'fin_vacacional_ss' => $ftermino_ss,
                'inicio_especial' => null,
                'fin_especial' => null,
                'cierre_horarios' => 'S',
                'cierre_seleccion' => 'S',
                'inicio_sele_alumnos' => $finicio_est,
                'fin_sele_alumnos' => $ftermino_est,
                'inicio_vacacional' => $finicio_vac,
                'termino_vacacional' => $ftermino_vac,
                'inicio_cal_docentes' => $finicio_cap,
                'fin_cal_docentes' => $ftermino_cap,
                'ccarrera' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => null
            ]);
            return view('escolares.si');
        }
    }

    public function periodomodifica()
    {
        $per = $this->periodo();
        $periodo = $per[0]->periodo;
        $periodos = DB::table('periodos_escolares')
            ->orderBy('periodo', 'desc')
            ->get();
        return view('escolares.periodo_mod')->with(compact('periodo', 'periodos'));
    }

    public function periodomodificar(Request $request)
    {
        $periodo = $request->get('periodo');
        $periodos = DB::table('periodos_escolares')->where('periodo', $periodo)->first();
        return view('escolares.periodo_modifica')->with(compact('periodo', 'periodos'));
    }

    public function periodoupdate(Request $request)
    {
        $data = request()->validate([
            'finicio' => 'required',
            'ftermino' => 'required',
            'finicio_vac' => 'required',
            'ftermino_vac' => 'required',
            'finicio_cap' => 'required',
            'ftermino_cap' => 'required',
            'finicio_est' => 'required',
            'ftermino_est' => 'required'
        ], [
            'finicio.required' => 'Debe indicar la fecha de inicio del semestre',
            'ftermino.required' => 'Debe escribir la fecha de término del semestre',
            'finicio_vac.required' => 'Debe indicar la fecha de inicio de vacaciones para el semestre',
            'ftermino_vac.required' => 'Debe escribir la fecha de término de vacaciones para el semestre',
            'finicio_cap.required' => 'Debe indicar la fecha de inicio de captura docente para el semestre',
            'ftermino_cap.required' => 'Debe escribir la fecha de término de captura docente para el semestre',
            'finicio_est.required' => 'Debe indicar la fecha de inicio de selección de materias del estudiante para el semestre',
            'ftermino_est.required' => 'Debe escribir la fecha de término de selección de materias del estudiante para el semestre'
        ]);
        $periodo = $request->get('periodo');
        $ccarrera = $request->get('ccarrera');
        $finicio = $request->get('finicio');
        $ftermino = $request->get('ftermino');
        $finicio_ss1 = $request->get('finicio_ss');
        $ftermino_ss1 = $request->get('ftermino_ss');
        $finicio_ss = empty($finicio_ss1) ? null : $finicio_ss1;
        $ftermino_ss = empty($ftermino_ss1) ? null : $ftermino_ss1;
        $finicio_vac = $request->get('finicio_vac');
        $ftermino_vac = $request->get('ftermino_vac');
        $finicio_cap = $request->get('finicio_cap');
        $ftermino_cap = $request->get('ftermino_cap');
        $finicio_est = $request->get('finicio_est');
        $ftermino_est = $request->get('ftermino_est');
        $horarios = $request->get('horarios');
        $seleccion = $request->get('seleccion');
        DB::table('periodos_escolares')->where('periodo', $periodo)
            ->update([
                'fecha_inicio' => $finicio,
                'fecha_termino' => $ftermino,
                'inicio_vacacional_ss' => $finicio_ss,
                'fin_vacacional_ss' => $ftermino_ss,
                'cierre_horarios' => $horarios,
                'cierre_seleccion' => $seleccion,
                'inicio_sele_alumnos' => $finicio_est,
                'fin_sele_alumnos' => $ftermino_est,
                'inicio_vacacional' => $finicio_vac,
                'termino_vacacional' => $ftermino_vac,
                'inicio_cal_docentes' => $finicio_cap,
                'ccarrera' => $ccarrera,
                'fin_cal_docentes' => $ftermino_cap,
                'updated_at' => Carbon::now()
            ]);
        return view('escolares.si');
    }

    public function periodoactas1()
    {
        $per = $this->periodo();
        $periodo = $per[0]->periodo;
        $periodos = DB::table('periodos_escolares')
            ->orderBy('periodo', 'desc')
            ->get();
        return view('escolares.periodo_actas1')->with(compact('periodo', 'periodos'));
    }

    public function periodoactas2(Request $request)
    {
        $periodo = $request->get('periodo');
        $docentes = DB::table('grupos')->where('periodo', $periodo)
            ->join('personal', 'personal.rfc', '=', 'grupos.rfc')
            ->select('grupos.rfc', 'apellidos_empleado', 'nombre_empleado')
            ->distinct()
            ->orderBy('apellidos_empleado', 'asc')
            ->orderBy('nombre_empleado', 'asc')
            ->get();
        $nperiodo = DB::table('periodos_escolares')->where('periodo', $periodo)->first();
        return view('escolares.periodo_actas2')->with(compact('periodo', 'docentes', 'nperiodo'));
    }

    public function periodoactas3(Request $request)
    {
        $periodo = $request->get('periodo');
        $docente = $request->get('docente');
        $grupos = DB::table('grupos')->where('periodo', $periodo)
            ->where('rfc', $docente)
            ->join('materias', 'materias.materia', '=', 'grupos.materia')
            ->select('grupos.materia', 'grupo', 'nombre_abreviado_materia')
            ->orderBy('nombre_abreviado_materia', 'asc')
            ->get();
        $ndocente = DB::table('personal')->where('rfc', $docente)->first();
        $nperiodo = DB::table('periodos_escolares')->where('periodo', $periodo)->first();
        return view('escolares.periodo_actas3')->with(compact('periodo', 'docente', 'nperiodo', 'grupos', 'ndocente'));
    }

    public function modificaracta($periodo, $docente, $materia, $grupo)
    {
        $alumnos = DB::table('seleccion_materias')->where('periodo', $periodo)
            ->where('materia', $materia)
            ->where('grupo', $grupo)
            ->join('alumnos', 'alumnos.no_de_control', '=', 'seleccion_materias.no_de_control')
            ->distinct()
            ->select('seleccion_materias.no_de_control', 'apellido_paterno', 'apellido_materno', 'nombre_alumno', 'calificacion', 'tipo_evaluacion', 'plan_de_estudios')
            ->orderBy('apellido_paterno', 'asc')
            ->orderBy('apellido_materno', 'asc')
            ->orderBy('nombre_alumno', 'asc')
            ->get();
        $ndocente = DB::table('personal')->where('rfc', $docente)->first();
        $nperiodo = DB::table('periodos_escolares')->where('periodo', $periodo)->first();
        $nmateria = DB::table('materias')->where('materia', $materia)->first();
        $tipo_3 = DB::table('tipos_evaluacion')->where('plan_de_estudios', '3')
            ->where('tipo_evaluacion', '!=', 'AC')
            ->get();
        $tipo_4 = DB::table('tipos_evaluacion')->where('plan_de_estudios', '4')
            ->where('tipo_evaluacion', '!=', 'AC')
            ->get();
        return view('escolares.actas_modificar')
            ->with(compact('periodo', 'nperiodo', 'alumnos', 'ndocente', 'nmateria', 'materia', 'grupo', 'tipo_3', 'tipo_4'));
    }

    public function actasupdate(Request $request)
    {
        $materia = $request->get('materia');
        $grupo = $request->get('grupo');
        $periodo = $request->get('periodo');
        $inscritos = DB::table('seleccion_materias')
            ->where('periodo', $periodo)
            ->where('materia', $materia)
            ->where('grupo', $grupo)
            ->select('no_de_control')
            ->get();
        foreach ($inscritos as $alumnos) {
            $control = $alumnos->no_de_control;
            $obtener = $materia . "_" . $grupo . "_" . $control;
            $op = "op_" . $control;
            $cal = $request->get($obtener);
            $oport = $request->get($op);
            DB::table('seleccion_materias')
                ->where('periodo', $periodo)
                ->where('materia', $materia)
                ->where('grupo', $grupo)
                ->where('no_de_control', $control)
                ->update([
                    'calificacion' => $cal,
                    'tipo_evaluacion' => $oport,
                    'updated_at' => Carbon::now()
                ]);
            if (DB::table('historia_alumno')->where('periodo', $periodo)
                    ->where('materia', $materia)
                    ->where('grupo', $grupo)
                    ->where('no_de_control', $control)
                    ->count() > 0
            ) {
                DB::table('historia_alumno')->where('periodo', $periodo)
                    ->where('materia', $materia)
                    ->where('grupo', $grupo)
                    ->where('no_de_control', $control)
                    ->update([
                        'calificacion' => $cal,
                        'tipo_evaluacion' => $oport,
                        'updated_at' => Carbon::now()
                    ]);
            }
        }
        return view('escolares.si');
    }

    public function imprimiracta($periodo, $doc, $materia, $grupo)
    {
        if (DB::table('seleccion_materias')->where('periodo', $periodo)
                ->where('materia', $materia)
                ->where('grupo', $grupo)
                ->count() > 0) {
            if (DB::table('seleccion_materias')->where('periodo', $periodo)
                    ->where('materia', $materia)
                    ->where('grupo', $grupo)
                    ->whereNotNull('calificacion')
                    ->count() > 0) {
                $inscritos = DB::table('seleccion_materias')->where('periodo', $periodo)
                    ->where('materia', $materia)
                    ->where('grupo', $grupo)
                    ->join('alumnos', 'seleccion_materias.no_de_control', '=', 'alumnos.no_de_control')
                    ->join('tipos_evaluacion', function ($join) {
                        $join->on('alumnos.plan_de_estudios', '=', 'tipos_evaluacion.plan_de_estudios')
                            ->on('tipos_evaluacion.tipo_evaluacion', '=', 'seleccion_materias.tipo_evaluacion');
                    })
                    ->orderBy('apellido_paterno', 'asc')
                    ->orderBy('apellido_materno', 'asc')
                    ->orderBy('nombre_alumno', 'asc')
                    ->get();
                $datos = DB::table('grupos')->where('periodo', $periodo)
                    ->where('materia', $materia)
                    ->where('grupo', $grupo)
                    ->get();
                $nombre_mat = DB::table('materias')->where('materia', $materia)->get();
                $ndocente = Personal::where('rfc', $doc)
                    ->select('apellidos_empleado', 'nombre_empleado')->get();
                $nperiodo = DB::table('periodos_escolares')->where('periodo', $periodo)->get();
                $data = [
                    'alumnos' => $inscritos,
                    'docente' => $ndocente,
                    'nombre_periodo' => $nperiodo,
                    'datos' => $datos,
                    'nmateria' => $nombre_mat,
                    'materia' => $materia,
                    'grupo' => $grupo
                ];
                $pdf = PDF::loadView('escolares.pdf_acta', $data);
                return $pdf->download('acta.pdf');
            } else {
                $inscritos = DB::table('seleccion_materias')->where('periodo', $periodo)
                    ->where('materia', $materia)
                    ->where('grupo', $grupo)
                    ->join('alumnos', 'seleccion_materias.no_de_control', '=', 'alumnos.no_de_control')
                    ->orderBy('apellido_paterno', 'asc')
                    ->orderBy('apellido_materno', 'asc')
                    ->orderBy('nombre_alumno', 'asc')
                    ->get();
                $datos = DB::table('grupos')->where('periodo', $periodo)
                    ->where('materia', $materia)
                    ->where('grupo', $grupo)
                    ->get();
                $nombre_mat = DB::table('materias')->where('materia', $materia)->get();
                $ndocente = Personal::where('rfc', $doc)
                    ->select('apellidos_empleado', 'nombre_empleado')->get();
                $nperiodo = DB::table('periodos_escolares')->where('periodo', $periodo)->get();
                $data = [
                    'alumnos' => $inscritos,
                    'docente' => $ndocente,
                    'nombre_periodo' => $nperiodo,
                    'datos' => $datos,
                    'nmateria' => $nombre_mat,
                    'materia' => $materia,
                    'grupo' => $grupo
                ];
                $pdf = PDF::loadView('escolares.pdf_acta2', $data)
                    ->setPaper('Letter');
                return $pdf->download('acta.pdf');
            }
        } else {
            $mensaje = "No cuenta con alumnos inscritos en la materia";
            return view('personal.no')->with(compact('mensaje'));
        }
    }

    public function busqueda(Request $request)
    {
        $data = request()->validate([
            'control' => 'required',
        ], [
            'control.required' => 'Debe indicar un dato para ser buscado'
        ]);
        $id = $request->get('control');
        $tbusqueda = $request->get('tbusqueda');
        if ($tbusqueda == "1") {
            $alumno = Alumnos::findOrfail($id);
            $datos = AlumnosGenerales::findOrfail($id);
            $ncarrera = Db::table('carreras')->select('nombre_carrera')
                ->where('carrera', $alumno->carrera)
                ->where('reticula', $alumno->reticula)
                ->get();
            $periodo = $this->periodo();
            $periodos = DB::table('periodos_escolares')
                ->orderBy('periodo', 'desc')
                ->get();
            $ingreso = DB::table('periodos_escolares')->where('periodo', $alumno->periodo_ingreso_it)
                ->select('identificacion_corta')->first();
            $estatus = Db::table('estatus_alumno')->where('estatus', $alumno->estatus_alumno)->get();
            $espe = DB::table('especialidades')->where('especialidad', $alumno->especialidad)
                ->where('carrera', $alumno->carrera)->where('reticula', $alumno->reticula)->first();
            if (empty($espe)) {
                $especialidad = "POR ASIGNAR";
            } else {
                $especialidad = $espe->nombre_especialidad;
            }
            return view('escolares.datos')->
            with(compact('alumno', 'ncarrera', 'datos', 'id', 'periodo', 'periodos', 'estatus', 'especialidad', 'ingreso'));
        } elseif ($tbusqueda == '2') {
            $arroja = Alumnos::where('apellido_paterno', strtoupper($id))
                ->orWhere('apellido_materno', strtoupper($id))
                ->orWhere('nombre_alumno', strtoupper($id))
                ->orderBY('apellido_paterno')
                ->orderBy('apellido_materno')
                ->orderBy('nombre_alumno')
                ->get();
            $periodo = $this->periodo();
            $periodos = DB::table('periodos_escolares')
                ->orderBy('periodo', 'desc')
                ->get();
            return view('escolares.datos2')->with(compact('arroja', 'periodo', 'periodos'));
        }
    }

    public function accion(Request $request)
    {
        $control = $request->control;
        $periodo = $request->periodo;
        $accion = $request->accion;
        $alumno = Alumnos::findOrfail($control);
        $ncarrera = Db::table('carreras')->select('nombre_carrera', 'creditos_totales')
            ->where('carrera', $alumno->carrera)
            ->where('reticula', $alumno->reticula)
            ->get();
        $estatus = Db::table('estatus_alumno')->where('estatus', $alumno->estatus_alumno)->get();
        if ($accion == 1) {
            $calificaciones = $this->kardex($control);
            $nperiodos = $this->nperiodo($control);
            return view('escolares.kardex')
                ->with(compact('alumno', 'calificaciones', 'estatus', 'ncarrera', 'nperiodos', 'control'));
        } elseif ($accion == 2) {
            $historial = $this->reticula($control);
            return view('escolares.reticula')->with(compact('alumno', 'historial'));
        } elseif ($accion == 3) {
            if (DB::table('seleccion_materias')->where('periodo', $periodo)
                    ->where('no_de_control', $control)->count() > 0) {
                return view('escolares.preconstancia')->with(compact('alumno', 'periodo'));
            } else {
                $mensaje = "No se puede generar la constancia porque el estudiante no cuenta con carga académica";
                return view('escolares.no')->with(compact('mensaje'));
            }
        } elseif ($accion == 4) {
            if (DB::table('historia_alumno')
                    ->where('periodo', $periodo)
                    ->where('no_de_control', $control)
                    ->count() > 0) {
                $cal_periodo = $this->boleta($control, $periodo);
                $nombre_periodo = DB::table('periodos_escolares')->where('periodo', $periodo)->get();
                return view('escolares.boleta')
                    ->with(compact('alumno', 'cal_periodo', 'nombre_periodo', 'periodo'));
            } else {
                $mensaje = "El estudiante no cuenta con calificaciones registradas para el período señalado";
                return view('escolares.no')->with(compact('mensaje'));
            }
        } elseif ($accion == 5) {
            if (DB::table('seleccion_materias')
                    ->where('no_de_control', $control)
                    ->where('periodo', $periodo)
                    ->count() > 0) {
                $datos_horario = DB::select("select * from pac_horario('$control','$periodo')");
                $nombre_periodo = DB::table('periodos_escolares')->where('periodo', $periodo)->get();
                return view('escolares.horario')->with(compact('alumno', 'datos_horario', 'nombre_periodo', 'periodo'));
            } else {
                $mensaje = "NO CUENTA CON CARGA ACADÉMICA ASIGNADA";
                return view('escolares.no')->with(compact('mensaje'));
            }
        } elseif ($accion == 6) {
            $estatus_alumno = DB::table('estatus_alumno')->get();
            $nombre_periodo = DB::table('periodos_escolares')->where('periodo', $periodo)->first();
            return view('escolares.modificar_estatus')->with(compact('alumno', 'periodo', 'estatus_alumno', 'nombre_periodo', 'control'));
        } elseif ($accion == 7) {
            if (DB::table('avisos_reinscripcion')->where('periodo', $periodo)->where('no_de_control', $control)->count() > 0) {
                DB::table('avisos_reinscripcion')->where('periodo', $periodo)
                    ->where('no_de_control', $control)->update([
                        'autoriza_escolar' => 'S',
                        'recibo_pago' => '1',
                        'fecha_hora_seleccion' => Carbon::now(),
                        'encuesto' => 'S',
                        'updated_at' => Carbon::now()
                    ]);
            } else {
                $creditos = DB::table('carreras')->where('carrera', $alumno->carrera)
                    ->where('reticula', $alumno->reticula)->select('carga_minima')->first();
                $semestre = $this->semreal($alumno->periodo_ingreso_it, $periodo);
                DB::table('avisos_reinscripcion')->insert([
                    'periodo' => $periodo,
                    'no_de_control' => $control,
                    'autoriza_escolar' => 'S',
                    'recibo_pago' => '1',
                    'fecha_recibo' => null,
                    'cuenta_pago' => null,
                    'fecha_hora_seleccion' => Carbon::now(),
                    'lugar_seleccion' => null,
                    'fecha_hora_pago' => null,
                    'lugar_pago' => null,
                    'adeuda_escolar' => null,
                    'adeuda_biblioteca' => null,
                    'adeuda_financieros' => null,
                    'otro_mensaje' => null,
                    'baja' => null,
                    'motivo_aviso_baja' => null,
                    'egresa' => null,
                    'encuesto' => 'S',
                    'vobo_adelanta_sel' => null,
                    'regular' => null,
                    'indice_reprobacion' => 0,
                    'creditos_autorizados' => $creditos->carga_minima,
                    'estatus_reinscripcion' => null,
                    'semestre' => $semestre,
                    'promedio' => 0,
                    'adeudo_especial' => 'N',
                    'promedio_acumulado' => null,
                    'proareas' => null,
                    'created_at' => Carbon::now()
                ]);
                DB::table('alumnos')->where('no_de_control', $control)->update([
                    'semestre' => $semestre
                ]);
            }
            return view('escolares.si');
        } elseif ($accion == 8) {
            $especialidades = DB::table('especialidades')->where('carrera', $alumno->carrera)
                ->where('reticula', $alumno->reticula)->get();
            return view('escolares.modificar_especialidad')->with(compact('alumno', 'especialidades'));
        } elseif ($accion == 9) {
            $carreras = DB::table('carreras')->where('ofertar', '1')
                ->orderBy('nombre_carrera', 'ASC')->get();
            return view('escolares.modificar_carrera')->with(compact('alumno', 'carreras', 'control'));
        } elseif ($accion == 10) {
            return view('escolares.confirmar_borrado')->with(compact('alumno', 'control'));
        } elseif ($accion == 11) {
            return view('escolares.confirmar_bajatemp')->with(compact('alumno', 'periodo'));
        } elseif ($accion == 12) {
            return view('escolares.alta_nss')->with(compact('alumno'));
        } elseif ($accion == 13) {
            $mat = DB::table('materias_carreras')->where('carrera', $alumno->carrera)
                ->where('reticula', $alumno->reticula)
                ->join('materias', 'materias.materia', '=', 'materias_carreras.materia')
                ->where('nombre_completo_materia', 'LIKE', "%COMPLEMENTARIAS%")
                ->first();
            if (DB::table('historia_alumno')->where('no_de_control', $control)
                    ->where('materia', $mat->materia)->count() > 0) {
                $mensaje = "La materia ya está acreditada por lo que no es posible volverla a activar";
                return view('escolares.no')->with(compact('mensaje'));
            } else {
                DB::table('historia_alumno')->insert([
                    'periodo' => $periodo,
                    'no_de_control' => $control,
                    'materia' => $mat->materia,
                    'grupo' => null,
                    'calificacion' => 60,
                    'tipo_evaluacion' => 'AC',
                    'fecha_calificacion' => Carbon::now(),
                    'plan_de_estudios' => $alumno->plan_de_estudios,
                    'estatus_materia' => 'A',
                    'nopresento' => 'N',
                    'usuario' => Auth::user()->email,
                    'fecha_actualizacion' => Carbon::now(),
                    'periodo_acredita_materia' => $periodo,
                    'created_at' => Carbon::now(),
                    'updated_at' => null
                ]);
                return view('escolares.si');
            }
        } elseif ($accion == 14) {
            if (DB::table('idiomas_liberacion')->where('control', $control)->count() > 0) {
                return view('escolares.prelibidiomas')->with(compact('control', 'alumno'));
            } else {
                $mensaje = "No existe registro que el estudiante haya liberado idioma extranjero";
                return view('escolares.no')->with(compact('mensaje'));
            }
        } elseif ($accion == 15) {
            $periodos = DB::table('periodos_escolares')->orderBy('periodo', 'desc')->get();
            return view('escolares.datos_certificado')->with(compact('alumno', 'control', 'periodo', 'periodos'));
        } elseif ($accion == 16){
            $planes = DB::table('planes_de_estudio')->get();
            $alumno_plan=$alumno->plan_de_estudios;
            $periodos = DB::table('periodos_escolares')->orderBy('periodo', 'desc')->get();
            $periodo_ingreso=$alumno->periodo_ingreso_it;
            $tipos_ingreso=DB::table('tipos_ingreso')->get();
            $tipo_ingreso=$alumno->tipo_ingreso;
            $generales = AlumnosGenerales::findOrfail($control);
            return view('escolares.modificar_alumno')->with(compact('control','alumno','planes','periodos','periodo_ingreso','alumno_plan','tipo_ingreso','tipos_ingreso','generales'));
        }
    }

    public function imprimirboleta(Request $request)
    {
        $control = $request->control;
        $periodo = $request->periodo;
        $alumno = Alumnos::findOrfail($control);
        $cal_periodo = $this->boleta($alumno->no_de_control, $periodo);
        $nombre_periodo = DB::table('periodos_escolares')->where('periodo', $periodo)->get();
        $data = [
            'alumno' => $alumno,
            'cal_periodo' => $cal_periodo,
            'nombre_periodo' => $nombre_periodo,
            'periodo' => $periodo
        ];
        $pdf = PDF::loadView('escolares.pdf_boleta', $data)
            ->setPaper('Letter');
        return $pdf->download('boleta.pdf');
    }

    public function accionk(Request $request)
    {
        $control = $request->control;
        $accion = $request->accion;
        $alumno = Alumnos::findOrfail($control);
        if ($accion == 1) {
            $carga_acad = DB::select("SELECT * FROM cmaterias('$control')");
            $periodos = DB::table('periodos_escolares')
                ->orderBy('periodo', 'DESC')
                ->get();
            $tipo_ev = DB::table('tipos_evaluacion')
                ->where('plan_de_estudios', $alumno->plan_de_estudios)
                ->get();
            return view('escolares.akardex')
                ->with(compact('alumno', 'periodos', 'carga_acad', 'tipo_ev', 'control'));
        } elseif ($accion == 2) {
            $periodos = Db::table('historia_alumno')->where('no_de_control', $control)
                ->join('periodos_escolares', 'historia_alumno.periodo', '=', 'periodos_escolares.periodo')
                ->distinct('historia_alumno.periodo')
                ->select('historia_alumno.periodo', 'identificacion_corta')
                ->get();
            return view('escolares.m1kardex')->with(compact('alumno', 'periodos', 'control'));
        } elseif ($accion == 3) {
            $control = $request->control;
            $alumno = Alumnos::findOrfail($control);
            $calificaciones = $this->kardex($control);
            $carrera = DB::table('carreras')->where('carrera', $alumno->carrera)
                ->where('reticula', $alumno->reticula)->first();
            $nperiodos = $this->nperiodo($control);
            $data = [
                'alumno' => $alumno,
                'control' => $control,
                'carrera' => $carrera,
                'nperiodos' => $nperiodos,
                'calificaciones' => $calificaciones
            ];
            $pdf = PDF::loadView('escolares.pdf_kardex', $data);
            return $pdf->download('kardex.pdf');
        }
    }

    public function accionkalta(Request $request)
    {
        $data = request()->validate([
            'calif' => 'required',
        ], [
            'calif.required' => 'Debe indicar una calificacion'
        ]);
        $id = $request->get('control');
        $materia = $request->get('alta');
        $calif = $request->get('calif');
        $periodo = $request->get('nper');
        $tipo_ev = $request->get('tipo_e');
        $alumno = Alumnos::findOrfail($id);
        if (DB::table('historia_alumno')->where('no_de_control', $id)
                ->where('materia', $materia)
                ->where('periodo', $periodo)
                ->count() > 0) {
            $mensaje = "Ya está registrado el dato en el kardex del estudiante";
            return view('escolares.no')->with(compact('mensaje'));
        } else {
            $ha = new HistoriaAlumno;
            $ha->periodo = $periodo;
            $ha->no_de_control = $id;
            $ha->materia = $materia;
            $ha->calificacion = $calif;
            $ha->tipo_evaluacion = $tipo_ev;
            $ha->fecha_calificacion = date('Y-m-d H:i:s');
            $ha->plan_de_estudios = $alumno->plan_de_estudios;
            if ($calif >= 70 || ($tipo_ev == 'AC' || $tipo_ev == 'CE' || $tipo_ev == 'RU')) {
                $ha->estatus_materia = 'A';
            } else {
                $ha->estatus_materia = 'R';
            }
            $ha->usuario = Auth::user()->email;
            $ha->save();
            return view('escolares.si');
        }
    }

    public function accionkperiodo(Request $request)
    {
        $control = $request->get('control');
        $alumno = Alumnos::findOrfail($control);
        $periodo = $request->get('pbusqueda');
        $nperiodo = DB::table('periodos_escolares')->where('periodo', $periodo)->first();
        $mat = DB::table('historia_alumno')->where('periodo', $periodo)
            ->where('no_de_control', $control)
            ->join('materias_carreras as mc', 'mc.materia', '=', 'historia_alumno.materia')
            ->where('mc.carrera', $alumno->carrera)
            ->where('mc.reticula', $alumno->reticula)
            ->join('materias', 'materias.materia', '=', 'mc.materia')
            ->join('tipos_evaluacion as te', 'te.tipo_evaluacion', '=', 'historia_alumno.tipo_evaluacion')
            ->where('te.plan_de_estudios', $alumno->plan_de_estudios)
            ->select('periodo', 'historia_alumno.materia', 'calificacion', 'nombre_abreviado_materia', 'historia_alumno.tipo_evaluacion', 'descripcion_corta_evaluacion')
            ->get();
        return view('escolares.m2kardex')->with(compact('alumno', 'nperiodo', 'mat', 'periodo', 'control'));
    }

    public function modificarkardex($periodo, $control, $materia)
    {
        $alumno = Alumnos::findOrfail($control);
        $mat = DB::table('historia_alumno')->where('periodo', $periodo)
            ->where('no_de_control', $control)
            ->where('historia_alumno.materia', $materia)
            ->join('materias', 'materias.materia', '=', 'historia_alumno.materia')
            ->join('tipos_evaluacion as te', 'te.tipo_evaluacion', '=', 'historia_alumno.tipo_evaluacion')
            ->where('te.plan_de_estudios', $alumno->plan_de_estudios)
            ->select('calificacion', 'nombre_abreviado_materia', 'historia_alumno.tipo_evaluacion')
            ->first();
        $periodos = DB::table('periodos_escolares')->get();
        $tipos = DB::table('tipos_evaluacion')->where('plan_de_estudios', $alumno->plan_de_estudios)->get();
        return view('escolares.modificar_kardex')->with(compact('alumno', 'periodo', 'mat', 'materia', 'periodos', 'tipos', 'control'));
    }

    public function eliminarkardex($periodo, $control, $materia)
    {
        DB::table('historia_alumno')->where('no_de_control', $control)->where('periodo', $periodo)
            ->where('materia', $materia)->delete();
        $alumno = Alumnos::findOrfail($control);
        $datos = AlumnosGenerales::findOrfail($control);
        $ncarrera = Db::table('carreras')->select('nombre_carrera')
            ->where('carrera', $alumno->carrera)
            ->where('reticula', $alumno->reticula)
            ->get();
        $periodo = $this->periodo();
        $periodos = DB::table('periodos_escolares')
            ->orderBy('periodo', 'desc')
            ->get();
        $estatus = Db::table('estatus_alumno')->where('estatus', $alumno->estatus_alumno)->get();
        $espe = DB::table('especialidades')->where('especialidad', $alumno->especialidad)
            ->where('carrera', $alumno->carrera)->where('reticula', $alumno->reticula)->first();
        if (empty($espe)) {
            $especialidad = "POR ASIGNAR";
        } else {
            $especialidad = $espe->nombre_especialidad;
        }
        $id = $control;
        $ingreso = DB::table('periodos_escolares')->where('periodo', $alumno->periodo_ingreso_it)
            ->select('identificacion_corta')->first();
        return view('escolares.datos')->
        with(compact('alumno', 'ncarrera', 'datos', 'id', 'periodo', 'periodos', 'estatus', 'especialidad', 'ingreso'));
    }

    public function kardexupdate(Request $request)
    {
        $materia = $request->get('materia');
        $control = $request->get('control');
        $tipo_ev = $request->get('tipo_ev');
        $periodo_n = $request->get('periodo');
        $calif = $request->get('calificacion');
        $periodo_o = $request->get('periodo_o');
        DB::table('historia_alumno')->where('no_de_control', $control)
            ->where('materia', $materia)->where('periodo', $periodo_o)->update([
                'calificacion' => $calif,
                'periodo' => $periodo_n,
                'tipo_evaluacion' => $tipo_ev,
                'updated_at' => Carbon::now()
            ]);
        $alumno = Alumnos::findOrfail($control);
        $datos = AlumnosGenerales::findOrfail($control);
        $ncarrera = Db::table('carreras')->select('nombre_carrera')
            ->where('carrera', $alumno->carrera)
            ->where('reticula', $alumno->reticula)
            ->get();
        $periodo = $this->periodo();
        $periodos = DB::table('periodos_escolares')
            ->orderBy('periodo', 'desc')
            ->get();
        $ingreso = DB::table('periodos_escolares')->where('periodo', $alumno->periodo_ingreso_it)
            ->select('identificacion_corta')->first();
        $estatus = Db::table('estatus_alumno')->where('estatus', $alumno->estatus_alumno)->get();
        $espe = DB::table('especialidades')->where('especialidad', $alumno->especialidad)
            ->where('carrera', $alumno->carrera)->where('reticula', $alumno->reticula)->first();
        if (empty($espe)) {
            $especialidad = "POR ASIGNAR";
        } else {
            $especialidad = $espe->nombre_especialidad;
        }
        $id = $control;
        return view('escolares.datos')->
        with(compact('alumno', 'ncarrera', 'datos', 'id', 'periodo', 'periodos', 'estatus', 'especialidad', 'ingreso'));
    }

    public function estatusupdate(Request $request)
    {
        $periodo = $request->get('periodo');
        $control = $request->get('control');
        $estatus = $request->get('estatus');
        DB::table('alumnos')->where('no_de_control', $control)->update([
            'estatus_alumno' => $estatus
        ]);
        return view('escolares.si');
    }

    public function especialidadupdate(Request $request)
    {
        $control = $request->get('control');
        $especialidad = $request->get('espe');
        DB::table('alumnos')->where('no_de_control', $control)->update([
            'especialidad' => $especialidad
        ]);
        return view('escolares.si');
    }

    public function carreraupdate(Request $request)
    {
        $control = $request->get('control');
        $alumno = Alumnos::findOrfail($control);
        $carrera_n0 = $request->get('carrera_n');
        $data = explode("_", $carrera_n0);
        $carrera_n = $data[0];
        $ret_n = $data[1];
        $materias = DB::table('historia_alumno')->where('no_de_control', $control)
            ->select('periodo', 'materia', 'tipo_evaluacion')->get();
        $i = 0;
        $plan = DB::table('planes_de_estudio')->max('plan_de_estudio');
        foreach ($materias as $historia) {
            $cve_of = DB::table('materias_carreras')->where('carrera', $alumno->carrera)
                ->where('reticula', $alumno->reticula)->where('materia', $historia->materia)->first();
            if (!empty($cve_of)) {
                if (DB::table('materias_carreras')->where('carrera', $carrera_n)->where('reticula', $ret_n)
                        ->where('clave_oficial_materia', trim($cve_of->clave_oficial_materia))->count() > 0) {
                    $nmat = DB::table('materias_carreras')->where('carrera', $carrera_n)->where('reticula', $ret_n)
                        ->where('clave_oficial_materia', $cve_of->clave_oficial_materia)->select('materia')->first();
                    DB::table('historia_alumno')->where('no_de_control', $control)->where('periodo', $historia->periodo)
                        ->where('materia', $historia->materia)->update([
                            'materia' => $nmat->materia,
                            'tipo_evaluacion' => 'RC',
                            'plan_de_estudios' => $plan
                        ]);
                    $i++;
                } else {
                    DB::table('historia_alumno')->where('no_de_control', $control)->where('periodo', $historia->periodo)
                        ->where('materia', $historia->materia)->delete();
                }
            }
        }
        DB::table('alumnos')->where('no_de_control', $control)->update([
            'carrera' => $carrera_n,
            'reticula' => $ret_n,
            'plan_de_estudios' => $plan
        ]);
        return view('escolares.ccarrera_resultado')->with(compact('i'));
    }

    public function alumnodelete(Request $request)
    {
        $control = $request->get('control');
        //Primero, checar si tiene materias activas
        DB::table('seleccion_materias')->where('no_de_control', $control)->delete();
        //Ahora, se borra su historial
        DB::table('historia_alumno')->where('no_de_control', $control)->delete();
        //Borrar datos generales
        DB::table('alumnos_generales')->where('no_de_control', $control)->delete();
        //Eliminar alumno
        DB::table('alumnos')->where('no_de_control', $control)->delete();
        return view('escolares.si');
    }

    public function alumnobajatemp(Request $request)
    {
        $control = $request->get('control');
        $periodo = $request->get('periodo');
        $tbaja = $request->get('tbaja');
        //Primero, checar si tiene materias activas
        DB::table('seleccion_materias')->where('no_de_control', $control)
            ->where('periodo', $periodo)->delete();
        DB::table('alumnos')->where('no_de_control', $control)->update([
            'estatus_alumno' => $tbaja
        ]);
        return view('escolares.si');
    }

    public function alumnonss(Request $request)
    {
        $data = request()->validate([
            'nss' => 'required',
        ], [
            'nss.required' => 'Debe indicar el NSS ha ser registrado'
        ]);
        $control = $request->get('control');
        $nss = trim($request->get('nss'));
        DB::table('alumnos')->where('no_de_control', $control)->update([
            'nss' => $nss
        ]);
        return view('escolares.si');
    }

    public function reinscripcion()
    {
        $periodos = DB::table('periodos_escolares')->orderBy('periodo', 'desc')->get();
        $periodo_actual = $this->periodo();
        $periodo = $periodo_actual[0]->periodo;
        $carreras = DB::table('carreras')->distinct('carrera')->orderBy('carrera', 'asc')
            ->get();
        return view('escolares.prereinscripcion')->with(compact('periodos', 'periodo', 'carreras'));
    }

    public function accion_re(Request $request)
    {
        $periodo = $request->get('periodo');
        $carrera = $request->get('carrera');
        $accion = $request->get('accion');
        if ($accion == 1) {
            if (DB::table('fechas_carreras')->where('carrera', $carrera)
                    ->where('periodo', $periodo)->count() > 0) {
                $mensaje = "Ya registró una fecha para la carrera";
                return view('escolares.no')->with(compact('mensaje'));
            } else {
                $nperiodo = DB::table('periodos_escolares')->where('periodo', $periodo)
                    ->first();
                $ncarrera = DB::table('carreras')->where('carrera', $carrera)
                    ->select('nombre_reducido')->first();
                return view('escolares.fechas_re')->with(compact('periodo', 'carrera', 'nperiodo', 'ncarrera'));
            }
        } elseif ($accion == 2) {
            $anio_extraido = substr($periodo, 0, 4);
            $numero_periodo = (substr($periodo, 4, 1) == '3' || substr($periodo, 4, 1) == '2') ? '1' : '3';
            $anio = $numero_periodo == '1' ? $anio_extraido : $anio_extraido - 1;
            $periodo_anterior = $anio . $numero_periodo;
            if (DB::table('fechas_carreras')->where('periodo', $periodo)
                    ->where('carrera', $carrera)->count() > 0) {
                $valores = DB::table('fechas_carreras')->where('periodo', $periodo)
                    ->where('carrera', $carrera)->first();
                $fecha = $valores->fecha_inscripcion;
                $hora_inicio = $valores->fecha_inicio;
                $hora_fin = $valores->fecha_fin;
                $intervalo = $valores->intervalo;
                $personas = $valores->personas;
                $hora_inicio = substr($hora_inicio, 0, 2);
                if (substr($hora_inicio, 0, 1) == "0") {
                    $hora_inicio = substr($hora_inicio, 1, 1);
                }
                $hora_fin = substr($hora_fin, 0, 2);
                $tiempo_inicio = $hora_inicio;
                $inicio = $hora_inicio;
                $fin = $hora_fin;
                $sumadorT = 0;
                $j = 0;
                while ($inicio < $fin) {
                    if ($sumadorT < 60) {
                        if ($inicio < 10) {
                            if ($sumadorT > 0) {
                                $horas[$j] = "0" . $inicio . ":" . $sumadorT . ":00.0";
                            } else {
                                $horas[$j] = "0" . $inicio . ":00:00.0";
                            }
                        } else {
                            $horas[$j] = $inicio . ":" . $sumadorT . ":00.0";
                        }
                    } else {
                        $inicio += 1;
                        $sumadorT -= 60;
                        if ($sumadorT < 1) {
                            if ($inicio < 10) {
                                $horas[$j] = "0" . $inicio . ":00:00.0";
                            } else {
                                $horas[$j] = $inicio . ":00:00.0";
                            }
                        } else {
                            $horas[$j] = $inicio . ":" . $sumadorT . ":00.0";
                        }
                    }
                    $sumadorT += $intervalo;
                    $j++;
                }
                $para_horas = 1;
                $hora_puesta = 1;
                $p = 0;
                $avisos = DB::table('avisos_reinscripcion as AR')
                    ->where('periodo', $periodo)
                    ->join('alumnos as A', 'A.no_de_control', '=', 'AR.no_de_control')
                    ->where('A.estatus_alumno', 'ACT')
                    ->where('carrera', $carrera)
                    ->select('AR.no_de_control', 'A.apellido_paterno', 'A.apellido_materno', 'A.nombre_alumno', 'A.semestre', 'AR.fecha_hora_seleccion')
                    ->orderBy('A.semestre', 'asc')
                    ->get();
                $cont = 1;
                foreach ($avisos as $seleccion) {
                    if (DB::table('seleccion_materias')->where('no_de_control', $seleccion->no_de_control)
                            ->where('periodo', $periodo_anterior)->join('materias', 'materias.materia', '=', 'seleccion_materias.materia')
                            ->where('nombre_completo_materia', 'LIKE', "%RESIDENCIA%")->count() == 0) {
                        $consultar_promedio = DB::table('acumulado_historico')->where('periodo', $periodo_anterior)
                            ->where('no_de_control', $seleccion->no_de_control)->select('promedio_ponderado')
                            ->first();
                        if (empty($consultar_promedio)) {
                            $promedio_ponderado = 0;
                        } else {
                            $promedio_ponderado = trim($consultar_promedio->promedio_ponderado);
                            $promedio_ponderado = substr($promedio_ponderado, 0, 5);
                        }
                        //if ($promedio_ponderado==""){}
                        DB::table('generar_listas_temporales')->insert([
                            'no_de_control' => $seleccion->no_de_control,
                            'apellido_paterno' => $seleccion->apellido_paterno,
                            'apellido_materno' => $seleccion->apellido_materno,
                            'nombre_alumno' => $seleccion->nombre_alumno,
                            'semestre' => $seleccion->semestre,
                            'promedio_ponderado' => $promedio_ponderado
                        ]);
                        $cont++;
                    }
                }
                $consulta = Db::table('generar_listas_temporales')
                    ->orderBy('semestre', 'asc')
                    ->orderBy('promedio_ponderado', 'desc')
                    ->get();
                foreach ($consulta as $resultado) {
                    if ($hora_puesta < $personas) {
                        $fecha_asig = $fecha . " " . $horas[$p];
                        $hora_puesta++;
                    } else {
                        $fecha_asig = $fecha . " " . $horas[$p];
                        $hora_puesta = 1;
                        $p++;
                    }
                    $no_de_control = $resultado->no_de_control;
                    DB::table('avisos_reinscripcion')
                        ->where('no_de_control', $no_de_control)
                        ->where('periodo', $periodo)
                        ->update([
                            'fecha_hora_seleccion' => $fecha_asig
                        ]);
                }
                DB::table('generar_listas_temporales')->delete();
                return redirect('/escolares/reinscripcion');
            } else {
                $mensaje = "No ha indicado la fecha de reinscripción para la carrera";
                return view('escolares.no')->with(compact('mensaje'));
            }
        } elseif ($accion == 3) {
            $avisos = DB::table('avisos_reinscripcion as AR')
                ->where('periodo', $periodo)
                ->join('alumnos as A', 'A.no_de_control', '=', 'AR.no_de_control')
                ->where('A.estatus_alumno', 'ACT')
                ->where('carrera', $carrera)
                ->whereNotNull('AR.fecha_hora_seleccion')
                ->select('AR.no_de_control', 'A.apellido_paterno', 'A.apellido_materno', 'A.nombre_alumno', 'A.semestre', 'AR.fecha_hora_seleccion')
                ->orderBy('A.semestre', 'asc')
                ->orderBy('A.apellido_paterno', 'asc')
                ->orderBy('A.apellido_materno', 'asc')
                ->orderBy('A.no_de_control', 'asc')
                ->get();
            $nperiodo = DB::table('periodos_escolares')->where('periodo', $periodo)
                ->select('identificacion_corta')->first();
            $ncarrera = DB::table('carreras')->where('carrera', $carrera)->select('nombre_reducido')->first();
            $data = [
                'alumnos' => $avisos,
                'nperiodo' => $nperiodo,
                'ncarrera' => $ncarrera
            ];
            $pdf = PDF::loadView('escolares.pdf_listado', $data)
                ->setPaper('Letter');
            return $pdf->download('listado.pdf');
        }
    }

    public function altaf_re(Request $request)
    {
        $data = request()->validate([
            'dia' => 'required',
            'horaini' => 'required',
            'horafin' => 'required'
        ], [
            'dia.required' => 'Debe indicar el día para la reinscripción de la carrera',
            'horaini.required' => 'Debe indicar la hora en la que inicia la reinscripción de la carrera',
            'horafin.required' => 'Debe indicar la hora en la que termina la reinscripción de la carrera'
        ]);
        $carrera = $request->get('carrera');
        $periodo = $request->get('periodo');
        $dia = $request->get('dia');
        $horaini = $request->get('horaini');
        $horafin = $request->get('horafin');
        $intervalo = $request->get('intervalo');
        $personas = $request->get('personas');
        DB::table('fechas_carreras')->insert([
            'carrera' => $carrera,
            'fecha_inscripcion' => $dia,
            'fecha_inicio' => $horaini,
            'fecha_fin' => $horafin,
            'intervalo' => $intervalo,
            'personas' => $personas,
            'periodo' => $periodo,
            'puntero' => 0
        ]);
        return redirect('/escolares/reinscripcion');
    }

    public function prepoblacion()
    {
        $periodos = DB::table('periodos_escolares')->orderBy('periodo', 'desc')->get();
        $periodo_actual = $this->periodo();
        $periodo = $periodo_actual[0]->periodo;
        return view('escolares.prepoblacion')->with(compact('periodos', 'periodo'));
    }

    public function poblacion(Request $request)
    {
        $periodo = $request->get('periodo');
        $inscritos = $this->inscritos($periodo);
        $nperiodo = DB::table('periodos_escolares')->where('periodo', $periodo)->first();
        return view('escolares.poblacion')->with(compact('inscritos', 'periodo', 'nperiodo'));
    }

    public function pobxcarrera($periodo, $carrera, $reticula)
    {
        $nperiodo = DB::table('periodos_escolares')->where('periodo', $periodo)->first();
        $ncarrera = DB::table('carreras')->where('carrera', $carrera)->where('reticula', $reticula)->first();
        $hombres = array_fill(1, 10, 0);
        $mujeres = array_fill(1, 10, 0);
        $semestres = array(1 => "1", 2 => "2", 3 => "3", 4 => "4", 5 => "5", 6 => "6", 7 => "7", 8 => "8", 9 => "9", 10 => ">9");
        $pob_masc = DB::table('seleccion_materias')->where('periodo', $periodo)
            ->join('alumnos', 'alumnos.no_de_control', '=', 'seleccion_materias.no_de_control')
            ->where('carrera', $carrera)
            ->where('reticula', $reticula)
            ->where('sexo', 'M')
            ->select('seleccion_materias.no_de_control', 'periodo_ingreso_it')
            ->distinct()
            ->get();
        $pob_fem = DB::table('seleccion_materias')->where('periodo', $periodo)
            ->join('alumnos', 'alumnos.no_de_control', '=', 'seleccion_materias.no_de_control')
            ->where('carrera', $carrera)
            ->where('reticula', $reticula)
            ->where('sexo', 'F')
            ->select('seleccion_materias.no_de_control', 'periodo_ingreso_it')
            ->distinct()
            ->get();
        foreach ($pob_masc as $key => $value) {
            $periodo_ingreso = $value->periodo_ingreso_it;
            $semestre = $this->semreal($periodo_ingreso, $periodo);
            switch ($semestre) {
                case 1:
                    $hombres[1]++;
                    break;
                case 2:
                    $hombres[2]++;
                    break;
                case 3:
                    $hombres[3]++;
                    break;
                case 4:
                    $hombres[4]++;
                    break;
                case 5:
                    $hombres[5]++;
                    break;
                case 6:
                    $hombres[6]++;
                    break;
                case 7:
                    $hombres[7]++;
                    break;
                case 8:
                    $hombres[8]++;
                    break;
                case 9:
                    $hombres[9]++;
                    break;
                case ($semestre > 9):
                    $hombres[10]++;
                    break;
            }
        }
        foreach ($pob_fem as $key => $value) {
            $periodo_ingreso = $value->periodo_ingreso_it;
            $semestre = $this->semreal($periodo_ingreso, $periodo);
            switch ($semestre) {
                case 1:
                    $mujeres[1]++;
                    break;
                case 2:
                    $mujeres[2]++;
                    break;
                case 3:
                    $mujeres[3]++;
                    break;
                case 4:
                    $mujeres[4]++;
                    break;
                case 5:
                    $mujeres[5]++;
                    break;
                case 6:
                    $mujeres[6]++;
                    break;
                case 7:
                    $mujeres[7]++;
                    break;
                case 8:
                    $mujeres[8]++;
                    break;
                case 9:
                    $mujeres[9]++;
                    break;
                case ($semestre > 9):
                    $mujeres[10]++;
                    break;
            }
        }
        return view('escolares.poblacion2')->with(compact('semestres', 'hombres', 'mujeres', 'ncarrera', 'reticula', 'nperiodo'));
    }

    public function contrasenia()
    {
        return view('escolares.contrasenia');
    }

    public function ccontrasenia(Request $request)
    {
        $data = request()->validate([
            'contra' => 'required|required_with:verifica|same:verifica',
            'verifica' => 'required'
        ], [
            'contra.required' => 'Debe escribir la nueva contraseña',
            'contra.required_with' => 'Debe confirmar la contraseña',
            'contra.same' => 'No concuerda con la verificacion',
            'verifica.required' => 'Debe confirmar la nueva contraseña'
        ]);
        $ncontra = bcrypt($request->get('contra'));
        $data = Auth::user()->email;
        DB::table('users')->where('email', $data)->update([
            'password' => $ncontra,
            'updated_at' => Carbon::now()
        ]);
        return view('escolares.inicio');
    }

    public function carrerasalta()
    {
        $cant = DB::table('carreras')->select('carrera')->where('nivel_escolar', 'L')
            ->distinct('carrera')->count();
        return view('escolares.carrera_alta')->with(compact('cant'));
    }

    public function carreranueva(Request $request)
    {
        $data = request()->validate([
            'carrera' => 'required',
            'reticula' => 'required',
            'cve' => 'required',
            'ncarrera' => 'required',
            'nreducido' => 'required',
            'siglas' => 'required',
            'cred_max' => 'required',
            'cred_min' => 'required',
            'cred_tot' => 'required'
        ], [
            'carrera.required' => 'Debe escribir la clave para la carrera',
            'reticula.required' => 'Debe indicar a que retícula corresponde la carrera',
            'cve.required' => 'Debe indicar la clave oficial de la carrera',
            'ncarrera.required' => 'Debe indicar el nombre completo de la carrera',
            'nreducido.required' => 'Debe indicar el nombre abreviado para la carrera',
            'siglas.required' => 'Debe indicar las siglas para la carrera',
            'cred_max.required' => 'Debe indicar la carga máxima en créditos para la carrera',
            'cred_min.required' => 'Debe indicar la carga mínima en créditos para la carrera',
            'cred_tot.required' => 'Debe indicar la carga total que consta para la carrera'
        ]);
        $carrera = $request->get('carrera');
        $reticula = $request->get('reticula');
        if (DB::table('carreras')->where('carrera', $carrera)
                ->where('reticula', $reticula)->count() > 0) {
            $mensaje = "Ya existe una carrera con la misma retícula dada de alta, por lo que no fue posible
            crearla";
            return view('escolares.no')->with(compact('mensaje'));
        } else {
            $nivel = $request->get('nivel');
            $cve_oficial = $request->get('cve');
            $nombre_carrera = $request->get('ncarrera');
            $nombre_reducido = $request->get('nreducido');
            $siglas = $request->get('siglas');
            $cred_max = $request->get('cred_max');
            $cred_min = $request->get('cred_min');
            $cred_tot = $request->get('cred_tot');
            $modalidad = $request->get('modalidad');
            DB::table('carreras')->insert([
                'carrera' => $carrera,
                'reticula' => $reticula,
                'nivel_escolar' => $nivel,
                'clave_oficial' => $cve_oficial,
                'nombre_carrera' => $nombre_carrera,
                'nombre_reducido' => $nombre_reducido,
                'siglas' => $siglas,
                'carga_maxima' => $cred_max,
                'carga_minima' => $cred_min,
                'creditos_totales' => $cred_tot,
                'modalidad' => $modalidad,
                'nreal' => $nombre_carrera,
                'ofertar' => 0,
                'abrev' => $siglas,
                'nombre_ofertar' => null,
                'created_at' => Carbon::now(),
                'updated_at' => null
            ]);
            return view('escolares.si');
        }
    }

    public function especialidadesalta()
    {
        $carreras = DB::table('carreras')->select('carrera', 'reticula', 'nombre_reducido')->get();
        return view('escolares.especialidad_alta')->with(compact('carreras'));
    }

    public function especialidadnueva(Request $request)
    {
        $data = request()->validate([
            'espe' => 'required',
            'nespecialidad' => 'required',
            'cred_especialidad' => 'required',
            'cred_optativos' => 'required'
        ], [
            'espe.required' => 'Debe escribir la clave para la especialidad',
            'nespecialidad.required' => 'Debe indicar el nombre de la especialidad',
            'cred_especialidad.required' => 'Debe indicar la carga en créditos para la especialidad',
            'cred_optativos.required' => 'Debe indicar la carga en créditos optativos (0 si no lleva)'
        ]);
        $info = $request->all();
        $datos = explode("_", $info["carrera"]);
        $carrera = trim($datos[0]);
        $reticula = $datos[1];
        if (DB::table('especialidades')->where('especialidad', $info["espe"])->count() > 0) {
            $mensaje = "Ya existe una especialidad con esa clave, por lo que no es posible duplicar la información";
            return view('escolares.no')->with(compact('mensaje'));
        } else {
            DB::table('especialidades')->insert([
                'especialidad' => $info["espe"],
                'carrera' => $carrera,
                'reticula' => $reticula,
                'nombre_especialidad' => $info["nespecialidad"],
                'creditos_optativos' => $info["cred_optativos"],
                'creditos_especialidad' => $info["cred_especialidad"],
                'activa' => 1
            ]);
            return view('escolares.si');
        }
    }

    public function materianueva()
    {
        $carreras = DB::table('carreras')->select('carrera', 'reticula', 'nombre_reducido')->get();
        return view('escolares.materias_alta')->with(compact('carreras'));
    }

    public function materiasacciones(Request $request)
    {
        $accion = $request->get('accion');
        $carr = $request->get('carrera');
        $datos = explode("_", $carr);
        $carrera = trim($datos[0]);
        $reticula = $datos[1];
        if ($accion == 1) {
            $acad = DB::table('organigrama')->where('area_depende', 'like', '110%')
                ->where('clave_area', 'like', '%00')
                ->get();
            $espe = DB::table('especialidades')->where('carrera', $carrera)->where('reticula', $reticula)->get();
            $materias = DB::table('materias_carreras')
                ->where('carrera', $carrera)->where('reticula', $reticula)
                ->join('materias', 'materias_carreras.materia', '=', 'materias.materia')
                ->whereNull('especialidad')
                ->select('materias_carreras.materia', 'nombre_abreviado_materia', 'creditos_materia', 'horas_teoricas', 'horas_practicas', 'semestre_reticula', 'renglon')
                ->get();
            $ncarrera = DB::table('carreras')->where('carrera', $carrera)->where('reticula', $reticula)
                ->first();
            return view('escolares.materia_nueva')->with(compact('carrera', 'reticula', 'acad', 'espe', 'materias', 'ncarrera'));
        } elseif ($accion == 3) {
            $espe = DB::table('especialidades')->where('carrera', $carrera)->where('reticula', $reticula)->get();
            $ncarrera = DB::table('carreras')->where('carrera', $carrera)->where('reticula', $reticula)
                ->first();
            return view('escolares.reticulas')->with(compact('carrera', 'reticula', 'espe', 'ncarrera'));
        }
    }

    public function materiaalta(Request $request)
    {
        $data = request()->validate([
            'cve' => 'required',
            'cve_of' => 'required',
            'nombre_completo' => 'required',
            'nombre_abrev' => 'required',
            'horas_teoricas' => 'required',
            'horas_practicas' => 'required',
            'creditos' => 'required',
            'certificado' => 'required'

        ], [
            'cve.required' => 'Debe escribir la clave interna para la materia',
            'cve_of.required' => 'Debe escribir la clave oficial de la materia',
            'nombre_completo.required' => 'Debe indicar el nombre completo para la materia',
            'nombre_abrev.required' => 'Debe indicar el nombre corto para la materia',
            'horas_teoricas.required' => 'Debe indicar el número de horas teóricas de la materia',
            'horas_practicas.required' => 'Debe indicar el número de horas prácticas de la materia',
            'creditos.required' => 'Indique la cantidad de créditos para la materia',
            'certificado.required' => 'Indique la ubicación de la materia en el certificado'
        ]);
        $info = $request->all();
        DB::table('materias')->insert([
            'materia' => $info["cve"],
            'nivel_escolar' => $info["nivel"],
            'tipo_materia' => $info["tipo_materia"],
            'clave_area' => $info["area"],
            'nombre_completo_materia' => $info["nombre_completo"],
            'nombre_abreviado_materia' => $info["nombre_abrev"],
            'caracterizacion' => null,
            'generales' => null,
            'created_at' => Carbon::now(),
            'updated_at' => null
        ]);
        $espe = $info["especialidad"] == 0 ? null : $info["especialidad"];
        DB::table('materias_carreras')->insert([
            'carrera' => $info["carrera"],
            'reticula' => $info["reticula"],
            'materia' => $info["cve"],
            'creditos_materia' => $info["creditos"],
            'horas_teoricas' => $info["horas_teoricas"],
            'horas_practicas' => $info["horas_practicas"],
            'orden_certificado' => $info["certificado"],
            'semestre_reticula' => $info["semestre"],
            'creditos_prerrequisito' => 0,
            'especialidad' => $espe,
            'clave_oficial_materia' => $info["cve_of"],
            'renglon' => $info["renglon"],
            'created_at' => Carbon::now(),
            'updated_at' => null
        ]);
        $acad = DB::table('organigrama')->where('area_depende', 'like', '110%')
            ->where('clave_area', 'like', '%00')
            ->get();
        $espe = DB::table('especialidades')->where('carrera', $info["carrera"])->where('reticula', $info["reticula"])->get();
        $materias = DB::table('materias_carreras')
            ->where('carrera', $info["carrera"])->where('reticula', $info["reticula"])
            ->join('materias', 'materias_carreras.materia', '=', 'materias.materia')
            ->whereNull('especialidad')
            ->select('materias_carreras.materia', 'nombre_abreviado_materia', 'creditos_materia', 'horas_teoricas', 'horas_practicas', 'semestre_reticula', 'renglon')
            ->get();
        $carrera = $info["carrera"];
        $reticula = $info["reticula"];
        $ncarrera = DB::table('carreras')->where('carrera', $info["carrera"])->where('reticula', $info["reticula"])
            ->first();
        return view('escolares.materia_nueva')->with(compact('carrera', 'reticula', 'acad', 'espe', 'materias', 'ncarrera'));
    }

    public function vistareticula(Request $request)
    {
        $carrera = $request->get('carrera');
        $reticula = $request->get('reticula');
        $especialidad = $request->get('espe');
        $materias = DB::table('materias_carreras')
            ->where('carrera', $carrera)->where('reticula', $reticula)
            ->join('materias', 'materias_carreras.materia', '=', 'materias.materia')
            ->where(function ($query) use ($especialidad) {
                $query->whereNull('especialidad')
                    ->orWhere('especialidad', '=', $especialidad);
            })
            ->select('materias_carreras.materia', 'nombre_abreviado_materia', 'creditos_materia', 'horas_teoricas', 'horas_practicas', 'semestre_reticula', 'renglon')
            ->get();
        $espe = DB::table('especialidades')->where('carrera', $carrera)
            ->where('reticula', $reticula)->where('especialidad', $especialidad)
            ->first();
        $ncarrera = DB::table('carreras')->where('carrera', $carrera)->where('reticula', $reticula)
            ->first();
        return view('escolares.reticula_vista')->with(compact('espe', 'materias', 'ncarrera'));
    }

    public function certificado(Request $request)
    {
        $data = request()->validate([
            'femision' => 'required',
            'iniciales' => 'required',
            'director' => 'required',
            'registro' => 'required',
            'libro' => 'required',
            'foja' => 'required',
            'fregistro' => 'required'
        ], [
            'femision.required' => 'Debe indicar la fecha de cuando se emite el certificado',
            'iniciales.required' => 'Debe indicar las iniciales del Jefe de Servicios Escolares',
            'director.required' => 'Debe indicar el nombre completo del(la) Director(a)',
            'registro.required' => 'Debe indicar el número de registro para el certificado',
            'libro.required' => 'Debe indicar el libro del registro del certificado',
            'foja.required' => 'Debe indicar la foja del registro del certificado',
            'fregistro.required' => 'Debe especificar la fecha del registro del certificado'
        ]);
        $info = $request->all();
        return view('escolares.imprimir_certificado')->with(compact('info'));
    }

    public function periodoactas_m1()
    {
        $per = $this->periodo();
        $periodo = $per[0]->periodo;
        $periodos = DB::table('periodos_escolares')
            ->orderBy('periodo', 'desc')
            ->get();
        return view('escolares.periodo_actas_1')->with(compact('periodo', 'periodos'));
    }

    public function periodoactas_m2(Request $request)
    {
        $periodo = $request->get('periodo');
        $docentes = DB::table('grupos')->where('periodo', $periodo)
            ->join('personal', 'personal.rfc', '=', 'grupos.rfc')
            ->select('grupos.rfc', 'apellidos_empleado', 'nombre_empleado')
            ->distinct()
            ->orderBy('apellidos_empleado', 'asc')
            ->orderBy('nombre_empleado', 'asc')
            ->get();
        $nperiodo = DB::table('periodos_escolares')->where('periodo', $periodo)->first();
        return view('escolares.periodo_actas_2')->with(compact('periodo', 'docentes', 'nperiodo'));
    }

    public function periodoactas_m3(Request $request)
    {
        $periodo = $request->get('periodo');
        $docente = $request->get('docente');
        $grupos = DB::table('grupos')->where('periodo', $periodo)
            ->where('rfc', $docente)
            ->join('materias', 'materias.materia', '=', 'grupos.materia')
            ->select('grupos.materia', 'grupo', 'nombre_abreviado_materia', 'entrego')
            ->orderBy('nombre_abreviado_materia', 'asc')
            ->get();
        $ndocente = DB::table('personal')->where('rfc', $docente)->first();
        $nperiodo = DB::table('periodos_escolares')->where('periodo', $periodo)->first();
        return view('escolares.periodo_actas_3')->with(compact('periodo', 'docente', 'nperiodo', 'grupos', 'ndocente'));
    }

    public function periodoactas_m4(Request $request)
    {
        $periodo = $request->get('periodo');
        $docente = $request->get('docente');
        foreach ($request->all() as $key => $value) {
            if (($key != "periodo") && ($key != "docente") && ($key != "_token")) {
                $info = explode("_", $key);
                $materia = $info[0];
                $gpo = $info[1];
                DB::table('grupos')->where('periodo', $periodo)
                    ->where('rfc', $docente)
                    ->where('materia', $materia)
                    ->where('grupo', $gpo)
                    ->update([
                        'entrego' => $value
                    ]);
            }
        }
        return view('escolares.si');
    }

    public function actas_mantenimiento()
    {
        $per = $this->periodo();
        $periodo = $per[0]->periodo;
        $periodos = DB::table('periodos_escolares')
            ->orderBy('periodo', 'desc')
            ->get();
        return view('escolares.actas_mantenimiento1')->with(compact('periodo', 'periodos'));
    }

    public function actas_estatus(Request $request)
    {
        $periodo = $request->get('periodo');
        $accion = $request->get('accion');
        if ($accion == 1) {
            $resultado = $this->sin_evaluar($periodo);
            $titulo = "Materias sin ser evaluadas";
        }elseif ($accion == 2) {
            $resultado = $this->evaluadas($periodo);
            $titulo = "Materias evaluadas";
        }elseif ($accion == 3) {
            $resultado = $this->actas_faltantes($periodo);
            $titulo = "Actas faltantes por entregar en Escolares";
        }
        $nperiodo = DB::table('periodos_escolares')->where('periodo', $periodo)->first();
        return view('escolares.actas_estatus')->with(compact('nperiodo', 'resultado', 'titulo'));
    }
    public function cierre(){
        $per = $this->periodo();
        $periodo = $per[0]->periodo;
        $periodos = DB::table('periodos_escolares')
            ->orderBy('periodo', 'desc')
            ->get();
        return view('escolares.cierre_index')->with(compact('periodo', 'periodos'));
    }
    public function cierre_accion(Request $request){
        $periodo=$request->get('periodo');
        $accion=$request->get('orden');
        if($accion==1){
            $quien = Auth::user()->email;
            $this->actualizar_kardex($periodo,$quien);
            return redirect('/escolares/cierre');
        }elseif($accion==2){
            $alumnos=DB::table('historia_alumno')->where('periodo',$periodo)
                ->select('no_de_control')->distinct()->get();
            foreach ($alumnos as $inscrito){
                $no_de_control=$inscrito->no_de_control;
                $this->calcular_promedios($periodo,$no_de_control);
            }
            return redirect('/escolares/cierre');
        }elseif($accion==3) {
            //Se requiere conocer el próximo periodo
            $anio = substr($periodo, 0, 4);
            $per = substr($periodo, -1);
            if ($per == 1) {
                $periodo_siguiente = $anio . "3";
            } elseif ($per == 3) {
                $periodo_siguiente = ($anio + 1) . "1";
            }
            $this->avisos_reinscripcion($periodo, $periodo_siguiente);
            return redirect('/escolares/cierre');
        }elseif ($accion==4){
            $this->actualizar_semestre($periodo);
            return redirect('/escolares/cierre');
        }elseif ($accion==5){
            $this->curso_especial($periodo);
            return redirect('/escolares/cierre');
        }
    }
    public function idiomas_lib1(){
        $idiomas=DB::table('idiomas')->get();
        return view('escolares.liberacion1')->with(compact('idiomas'));
    }
    public function idiomas_lib2(Request $request){
        $data = request()->validate([
            'control' => 'required'
        ], [
            'control.required' => 'Debe indicar el número de control'
        ]);
        $control=$request->get('control');
        $alumno = Alumnos::findOrfail($control);
        $idioma=$request->get('idioma');
        if(DB::table('idiomas_liberacion')->where('control',$control)->count()>0){
            $mensaje="No es posible continuar porque el estudiante ya cuenta con la liberación del idioma";
            return view('escolares.no')->with(compact('mensaje'));
        }else{
            $lengua_extranjera=DB::table('idiomas')->where('id',$idioma)->first();
            return view('escolares.liberar_idioma')->with(compact('control','idioma','alumno','lengua_extranjera'));
        }
    }
    public function idiomas_lib3(Request $request){
        $control=$request->get('control');
        $idioma=$request->get('idioma');
        $opcion=$request->get('opcion');
        DB::table('idiomas_liberacion')->insert([
            'periodo'=>null,
            'control'=>$control,
            'calif'=>null,
            'liberacion'=>null,
            'idioma'=>$idioma,
            'opcion'=>$opcion
        ]);
        $alumno = Alumnos::findOrfail($control);
        return view('escolares.prelibidiomas')->with(compact('control','alumno'));
    }
    public function idiomas_impre(){
        return view('escolares.idiomas_impresion');
    }
    public function idiomas_impre2(Request $request){
        $data = request()->validate([
            'control' => 'required'
        ], [
            'control.required' => 'Debe indicar el número de control'
        ]);
        $control=$request->get('control');
        if(DB::table('idiomas_liberacion')->where('control',$control)->count()==0){
            $mensaje="No es posible continuar porque el estudiante no cuenta con la liberación del idioma";
            return view('escolares.no')->with(compact('mensaje'));
        }else{
            $alumno = Alumnos::findOrfail($control);
            return view('escolares.prelibidiomas')->with(compact('control','alumno'));
        }
    }
    public function idiomas_consulta(){
        $per = $this->periodo();
        $periodo = $per[0]->periodo;
        $periodos = DB::table('periodos_escolares')
            ->orderBy('periodo', 'desc')
            ->get();
        $idiomas=DB::table('idiomas')->get();
        return view('escolares.idiomas_consulta1')->with(compact('periodo','periodos','idiomas'));
    }
    public function idiomas_consulta2(Request $request){
        $periodo=$request->get('periodo');
        $idioma=$request->get('idioma');
        if(DB::table('idiomas_grupos')->where('periodo',$periodo)
            ->where('idioma',$idioma)->count()>0){
            $nperiodo=DB::table('periodos_escolares')->where('periodo',$periodo)->first();
            $nidioma=DB::table('idiomas')->where('id',$idioma)->first();
            $info=$this->consulta_idiomas($periodo,$idioma);
            return view('escolares.idiomas_consulta2')->with(compact('nperiodo','nidioma','info'));
        }else{
            $mensaje="No hay grupos registrados para el periodo solicitado";
            return view('escolares.no')->with(compact('mensaje'));
        }
    }
    public function modificar_datos(Request $request){
        $data = request()->validate([
            'control' => 'required',
            'apmat' => 'required',
            'nombre' => 'required',
            'plan'=>'required',
            'ingreso'=>'required',
            'semestre' => 'required',
            'curp' => 'required',
            'tipo' => 'required'
        ], [
            'control.required' => 'Debe indicar el numero de control',
            'apmat.required' => 'Debe escribir el apellido materno',
            'nombre.required' => 'Debe escribir el nombre',
            'plan.required'=> 'Especifique el plan de estudios',
            'ingreso.required'=>'Especifique el período de ingreso',
            'semestre.required' => 'Debe indicar el semestre que se encuentra actualmente',
            'curp.required' => 'Debe escribir el CURP',
            'tipo.required' => 'Debe especificar el tipo de ingreso del estudiante'
        ]);
        $control=$request->get('control');
        $appat = $request->get('appat');
        $apmat = $request->get('apmat');
        $nombre = $request->get('nombre');
        $plan = $request->get('plan');
        $ingreso = $request->get('ingreso');
        $semestre = $request->get('semestre');
        $nss = $request->get('nss');
        $curp = $request->get('curp');
        $calle = $request->get('calle');
        $colonia = $request->get('colonia');
        $cp = $request->get('cp');
        $telcel = $request->get('telcel');
        $correo = $request->get('correo');
        $rev = $request->get('periodos_revalidacion');
        $tipo = $request->get('tipo');
        $quien = Auth::user()->email;
        DB::table('alumnos')->where('no_de_control',$control)
            ->update([
            'apellido_paterno' => $appat,
            'apellido_materno' => $apmat,
            'nombre_alumno' => $nombre,
            'semestre' => $semestre,
            'plan_de_estudios' => $plan,
            'curp_alumno' => $curp,
            'tipo_ingreso' => $tipo,
            'periodo_ingreso_it' => $ingreso,
            'correo_electronico' => $correo,
            'periodos_revalidacion' => $rev,
            'usuario' => $quien,
            'fecha_actualizacion' => null,
            'nss' => $nss,
            'created_at' => null,
            'updated_at' => Carbon::now()
        ]);
        DB::table('alumnos_generales')->where('no_de_control',$control)
            ->update([
            'domicilio_calle' => $calle,
            'domicilio_colonia' => $colonia,
            'codigo_postal' => $cp,
            'telefono' => $telcel,
            'created_at' => null,
            'updated_at' => Carbon::now()
        ]);
        return view('escolares.si');
    }
}
