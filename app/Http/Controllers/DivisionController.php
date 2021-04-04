<?php

namespace App\Http\Controllers;

use App\Alumnos;
use App\AlumnosGenerales;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DivisionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(){
        return view('dep.inicio');
    }
    public function periodo(){
        $periodo_actual=Db::Select('select periodo from pac_periodo_actual()');
        return $periodo_actual;
    }
    public function kardex($control){
        //Primero, busco los periodos que ha tenido
        $inscrito_en=DB::table('historia_alumno')
            ->select('periodo')
            ->where('no_de_control',$control)
            ->distinct()
            ->get();
        $calificaciones=array();
        foreach ($inscrito_en as $cuando){
            //Ahora, materias y calificaciones
            $data=DB::select("select * from pac_calificaciones('$control','$cuando->periodo')");
            $calificaciones[$cuando->periodo]=$data;
        }
        //dd($calificaciones);
        return $calificaciones;
    }
    public function boleta($control,$periodo){
        //Primero, busco los periodos que ha tenido
        $data=DB::select("select * from calificaciones('$periodo','$control')");
        return $data;
    }
    public function reticula($control){
        //Primero, busco los periodos que ha tenido
        $data=DB::select("select * from pac_reticulaalumno('$control')");
        return $data;
    }
    public function inscritos($periodo){
        $data=DB::select("select * from pac_poblacion('$periodo')");
        return $data;
    }
    public function nperiodo($control){
        //Primero, busco los periodos que ha tenido
        $inscrito_en=DB::table('historia_alumno')
            ->select('periodo')
            ->where('no_de_control',$control)
            ->distinct()
            ->get();
        $nombres=array();
        foreach ($inscrito_en as $cuando){
            //Ahora, nombres
            $data=DB::table('periodos_escolares')->where('periodo',$cuando->periodo)->get();
            $nombres[$cuando->periodo]=$data;
        }
        return $nombres;
    }
    public function cruce($periodo,$materia,$grupo,$docente,$dia){
        $verifica=DB::statement('select cruce from cruce_horario(:periodo,:materia,:grupo,:docente,:dia)',[
            'periodo'=>$periodo,
            'materia'=>$materia,
            'grupo'=>$grupo,
            'docente'=>$docente,
            'dia'=>$dia
        ]);
        return $verifica;
    }
    public function altagrupo(){
        $data=Auth::user()->email;
        $carreras=DB::table('permisos_carreras')
            ->where('email',$data)
            ->orderBy('nombre_carrera','asc')
            ->orderBy('reticula','asc')->get();
        $periodos=DB::table('periodos_escolares')->orderBy('periodo','desc')->get();
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        return view('dep.selcarrera')->with(compact('carreras','periodos','periodo'));
    }
    public function listado2(Request $request){
        $periodo=$request->get('periodo');
        $carr=$request->get('carrera');
        $data=explode('_',$carr);
        $carrera=$data[0]; $ret=$data[1];
        $ncarrera=DB::table('carreras')->where('carrera',$carrera)->where('reticula',$ret)->get();
        $listado=Db::table('materias_carreras')->where('carrera',$carrera)
            ->where('reticula',$ret)
            ->join('materias','materias_carreras.materia','=','materias.materia')
            ->where('nombre_completo_materia','not like','%RESIDENCIA%')
            ->where('nombre_completo_materia','not like','%SERVICIO SOC%')
            ->where('nombre_completo_materia','not like','%COMPLEMENT%')
            ->orderBy('semestre_reticula','asc')
            ->orderBy('nombre_completo_materia','asc')
            ->get();
        return view('dep.listado3')->with(compact('listado','ncarrera','carrera','ret','periodo'));
    }
    public function creargrupo1($periodo,$materia,$carrera,$ret){
        $ncarrera=DB::table('carreras')->where('carrera',$carrera)->where('reticula',$ret)->get();
        $nmateria=DB::table('materias_carreras')->where('carrera',$carrera)->where('reticula',$ret)
            ->where('materias_carreras.materia',$materia)
            ->join('materias','materias_carreras.materia','=','materias.materia')
            ->select('nombre_abreviado_materia','creditos_materia')->first();
        $aulas=DB::table('aulas')->where('estatus','A')->get();
        return view('dep.crear_grupo')->with(compact('materia','carrera','ncarrera','ret','nmateria','aulas','periodo'));
    }
    public function creargrupo2(Request $request){
        $data=request()->validate([
            'grupo'=>'required',
            'capacidad'=>'required',
            'slunes'=>'required_with:elunes',
            'smartes'=>'required_with:emartes',
            'smiercoles'=>'required_with:emiercoles',
            'sjueves'=>'required_with:ejueves',
            'sviernes'=>'required_with:eviernes',
            'ssabado'=>'required_with:esabado',
        ],[
            'grupo.required'=>'Debe indicar la clave del grupo',
            'capacidad.required'=>'Debe indicar la capacidad del grupo',
            'slunes.required_with'=>'Debe indicar la hora de salida para el lunes',
            'smartes.required_with'=>'Debe indicar la hora de salida para el martes',
            'smiercoles.required_with'=>'Debe indicar la hora de salida para el miércoles',
            'sjueves.required_with'=>'Debe indicar la hora de salida para el jueves',
            'sviernes.required_with'=>'Debe indicar la hora de salida para el viernes',
            'ssabado.required_with'=>'Debe indicar la hora de salida para el sabado',
        ]);
        $periodo=$request->get('periodo');
        $carrera=$request->get('carrera');
        $ret=$request->get('reticula');
        $materia=$request->get('materia');
        $grupo=$request->get('grupo');
        $creditos=$request->get('creditos');
        $capacidad=$request->get('capacidad');
        $elunes=$request->get('elunes'); if(!empty($elunes)){$elunes=Carbon::parse($elunes);}
        $emartes=$request->get('emartes'); if(!empty($emartes)){$emartes=Carbon::parse($emartes);}
        $emiercoles=$request->get('emiercoles'); if(!empty($emiercoles)){$emiercoles=Carbon::parse($emiercoles);}
        $ejueves=$request->get('ejueves'); if(!empty($ejueves)){$ejueves=Carbon::parse($ejueves);}
        $eviernes=$request->get('eviernes'); if(!empty($eviernes)){$eviernes=Carbon::parse($eviernes);}
        $esabado=$request->get('esabado'); if(!empty($esabado)){$esabado=Carbon::parse($esabado);}
        $slunes=$request->get('slunes'); if(!empty($slunes)){$slunes=Carbon::parse($slunes);}
        $smartes=$request->get('smartes'); if(!empty($smartes)){$smartes=Carbon::parse($smartes);}
        $smiercoles=$request->get('smiercoles'); if(!empty($smiercoles)){$smiercoles=Carbon::parse($smiercoles);}
        $sjueves=$request->get('sjueves'); if(!empty($sjueves)){$sjueves=Carbon::parse($sjueves);}
        $sviernes=$request->get('sviernes'); if(!empty($sviernes)){$sviernes=Carbon::parse($sviernes);}
        $ssabado=$request->get('ssabado'); if(!empty($ssabado)){$ssabado=Carbon::parse($ssabado);}
        $aula_l=$request->get('aula_l');
        $aula_m=$request->get('aula_m');
        $aula_mm=$request->get('aula_mm');
        $aula_j=$request->get('aula_j');
        $aula_v=$request->get('aula_v');
        $aula_s=$request->get('aula_s');
        if(!empty($elunes)){$hl=$elunes->diff($slunes)->format('%h');}else{$hl=0;}
        if(!empty($emartes)){$hm=$emartes->diff($smartes)->format('%h');}else{$hm=0;}
        if(!empty($emiercoles)){$hmm=$emiercoles->diff($smiercoles)->format('%h');}else{$hmm=0;}
        if(!empty($ejueves)){$hj=$ejueves->diff($sjueves)->format('%h');}else{$hj=0;}
        if(!empty($eviernes)){$hv=$eviernes->diff($sviernes)->format('%h');}else{$hv=0;}
        if(!empty($esabado)){$hs=$esabado->diff($ssabado)->format('%h');}else{$hs=0;}
        $total_horas=$hl+$hm+$hmm+$hj+$hv+$hs;
        if($total_horas==$creditos){
            //Que no sea un grupo repetido
            if(DB::table('grupos')->where('periodo',$periodo)
                    ->where('materia',$materia)->where('grupo',$grupo)->count()>0){
                $mensaje="Ya existe la materia y grupo dados de alta previamente, por lo que no se volvió a crear el grupo";
                return view('dep.no')->with(compact('mensaje'));
            }else{
                if(!empty($elunes)){
                    try{
                        DB::table('horarios')->insert([
                            'periodo'=>$periodo,
                            'rfc'=>null,
                            'tipo_horario'=>'D',
                            'dia_semana'=>2,
                            'hora_inicial'=>$elunes,
                            'hora_final'=>$slunes,
                            'materia'=>$materia,
                            'grupo'=>$grupo,
                            'aula'=>$aula_l,
                            'actividad'=>null,
                            'consecutivo'=>null,
                            'vigencia_inicio'=>null,
                            'vigencia_fin'=>null,
                            'consecutivo_admvo'=>null,
                            'tipo_personal'=>null,
                            'created_at'=>Carbon::now()
                        ]);
                    }catch (QueryException $e){
                        $mensaje="El aula se encuentra ocupada el día lunes".'<br>';
                        return view('dep.no')->with(compact('mensaje'));
                    }
                }
                if(!empty($emartes)){
                    try{
                        DB::table('horarios')->insert([
                            'periodo'=>$periodo,
                            'rfc'=>null,
                            'tipo_horario'=>'D',
                            'dia_semana'=>3,
                            'hora_inicial'=>$emartes,
                            'hora_final'=>$smartes,
                            'materia'=>$materia,
                            'grupo'=>$grupo,
                            'aula'=>$aula_m,
                            'actividad'=>null,
                            'consecutivo'=>null,
                            'vigencia_inicio'=>null,
                            'vigencia_fin'=>null,
                            'consecutivo_admvo'=>null,
                            'tipo_personal'=>null,
                            'created_at'=>Carbon::now()
                        ]);
                    }catch (QueryException $e){
                        $mensaje="El aula se encuentra ocupada el día martes".'<br>';
                        return view('dep.no')->with(compact('mensaje'));
                    }
                }
                if(!empty($emiercoles)){
                    try{
                        $hecho=DB::table('horarios')->insert([
                            'periodo'=>$periodo,
                            'rfc'=>null,
                            'tipo_horario'=>'D',
                            'dia_semana'=>4,
                            'hora_inicial'=>$emiercoles,
                            'hora_final'=>$smiercoles,
                            'materia'=>$materia,
                            'grupo'=>$grupo,
                            'aula'=>$aula_mm,
                            'actividad'=>null,
                            'consecutivo'=>null,
                            'vigencia_inicio'=>null,
                            'vigencia_fin'=>null,
                            'consecutivo_admvo'=>null,
                            'tipo_personal'=>null,
                            'created_at'=>Carbon::now()
                        ]);
                    }catch(QueryException $e){
                        $mensaje="El aula se encuentra ocupada el día miércoles".'<br>';
                        return view('dep.no')->with(compact('mensaje'));
                    }
                }
                if(!empty($ejueves)){
                    try{
                        DB::table('horarios')->insert([
                            'periodo'=>$periodo,
                            'rfc'=>null,
                            'tipo_horario'=>'D',
                            'dia_semana'=>5,
                            'hora_inicial'=>$ejueves,
                            'hora_final'=>$sjueves,
                            'materia'=>$materia,
                            'grupo'=>$grupo,
                            'aula'=>$aula_j,
                            'actividad'=>null,
                            'consecutivo'=>null,
                            'vigencia_inicio'=>null,
                            'vigencia_fin'=>null,
                            'consecutivo_admvo'=>null,
                            'tipo_personal'=>null,
                            'created_at'=>Carbon::now()
                        ]);
                    }catch (QueryException $e){
                        $mensaje="El aula se encuentra ocupada el día jueves".'<br>';
                        return view('dep.no')->with(compact('mensaje'));
                    }
                }
                if(!empty($eviernes)){
                    try{
                        DB::table('horarios')->insert([
                            'periodo'=>$periodo,
                            'rfc'=>null,
                            'tipo_horario'=>'D',
                            'dia_semana'=>6,
                            'hora_inicial'=>$eviernes,
                            'hora_final'=>$sviernes,
                            'materia'=>$materia,
                            'grupo'=>$grupo,
                            'aula'=>$aula_v,
                            'actividad'=>null,
                            'consecutivo'=>null,
                            'vigencia_inicio'=>null,
                            'vigencia_fin'=>null,
                            'consecutivo_admvo'=>null,
                            'tipo_personal'=>null,
                            'created_at'=>Carbon::now()
                        ]);
                    }catch(QueryException $e){
                        $mensaje="El aula se encuentra ocupada el día viernes".'<br>';
                        return view('dep.no')->with(compact('mensaje'));
                    }
                }
                if(!empty($esabado)){
                    try{
                        DB::table('horarios')->insert([
                            'periodo'=>$periodo,
                            'rfc'=>null,
                            'tipo_horario'=>'D',
                            'dia_semana'=>7,
                            'hora_inicial'=>$esabado,
                            'hora_final'=>$ssabado,
                            'materia'=>$materia,
                            'grupo'=>$grupo,
                            'aula'=>$aula_s,
                            'actividad'=>null,
                            'consecutivo'=>null,
                            'vigencia_inicio'=>null,
                            'vigencia_fin'=>null,
                            'consecutivo_admvo'=>null,
                            'tipo_personal'=>null,
                            'created_at'=>Carbon::now()
                        ]);
                    }catch (QueryException $e){
                        $mensaje="El aula se encuentra ocupada el día sábado".'<br>';
                        return view('dep.no')->with(compact('mensaje'));
                    }
                }
                DB::table('grupos')->insert([
                    'periodo'=>$periodo,
                    'materia'=>$materia,
                    'grupo'=>$grupo,
                    'estatus_grupo'=>null,
                    'capacidad_grupo'=>$capacidad,
                    'alumnos_inscritos'=>0,
                    'folio_acta'=>null,
                    'paralelo_de'=>null,
                    'exclusivo_carrera'=>$carrera,
                    'exclusivo_reticula'=>$ret,
                    'rfc'=>null,
                    'tipo_personal'=>'B',
                    'exclusivo'=>'no',
                    'entrego'=>0,
                    'created_at'=>Carbon::now()
                ]);
                return view('dep.si');
            }
        }else{
            $mensaje="No se pudo realizar la acción porque no concuerda el número de horas a impartir contra las que debe tener la materia";
            return view('dep.no')->with(compact('mensaje'));
        }
    }
    public function paralelo1(){
        $data=Auth::user()->email;
        $carrera_origen=DB::table('permisos_carreras')
            ->where('email',$data)
            ->orderBy('nombre_carrera','asc')
            ->orderBy('reticula','asc')->get();
        $periodos=DB::table('periodos_escolares')->orderBy('periodo','desc')->get();
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        $carreras=DB::table('carreras')->orderBy('nombre_carrera','asc')
            ->orderBy('reticula','asc')->get();
        return view('dep.altaparalelo1')->with(compact('carreras','carrera_origen','periodos','periodo'));
    }
    public function paralelo2(Request $request){
        $origen=$request->get('carrerao');
        $destino=$request->get('carrerap');
        $periodo=$request->get('periodo');
        $datos_o=explode("_",$origen);
        $carrera_o=$datos_o[0]; $ret_o=$datos_o[1];
        $datos_p=explode("_",$destino);
        $carrera_p=$datos_p[0]; $ret_p=$datos_p[1];
        $listado_o=Db::table('materias_carreras')->where('carrera',$carrera_o)
            ->where('reticula',$ret_o)
            ->join('grupos','materias_carreras.materia','=','grupos.materia')
            ->where('grupos.periodo',$periodo)
            ->whereNull('grupos.paralelo_de')
            ->join('materias','materias_carreras.materia','=','materias.materia')
            ->orderBy('semestre_reticula','asc')
            ->orderBy('nombre_completo_materia','asc')
            ->get();
        $listado_p=Db::table('materias_carreras')->where('carrera',$carrera_p)
            ->where('reticula',$ret_p)
            ->join('materias','materias_carreras.materia','=','materias.materia')
            ->where('nombre_completo_materia','not like','%RESIDENCIA%')
            ->where('nombre_completo_materia','not like','%SERVICIO SOC%')
            ->where('nombre_completo_materia','not like','%COMPLEMENT%')
            ->orderBy('nombre_completo_materia','asc')
            ->get();
        return view('dep.altaparalelo2')->with(compact('listado_o','listado_p','carrera_o','ret_o','carrera_p','ret_p','periodo'));
    }
    public function paralelo3(Request $request){
        $data=request()->validate([
            'gpo_p'=>'required',
            'cap_n'=>'required'
        ],[
            'gpo_p.required'=>'Debe indicar la clave del grupo',
            'cap_n.required'=>'Debe indicar la capacidad del grupo'
        ]);
        $car_o=$request->get('carrera_o');
        $ret_o=$request->get('ret_o');
        $car_p=$request->get('carrera_p');
        $ret_p=$request->get('ret_p');
        $gpo_p=$request->get('gpo_p');
        $cap_n=$request->get('cap_n');
        $periodo=$request->get('periodo');
        $origenn=$request->get('mat_o');
        $datos_o=explode("_",$origenn);
        $mat_o=$datos_o[0];
        $gpo_o=$datos_o[1];
        $mat_p=$request->get('matp');
        //Se checa si existe el docente
        $doc=DB::table('grupos')->where('periodo',$periodo)->where('materia',$mat_o)->where('grupo',$gpo_o)
            ->select('rfc')->get();
        if(!empty($doc)){
            $rfc=$doc[0]->rfc;
        }else{
            $rfc=null;
        }
        //
        DB::table('grupos')->insert([
            'periodo'=>$periodo,
            'materia'=>$mat_p,
            'grupo'=>$gpo_p,
            'estatus_grupo'=>null,
            'capacidad_grupo'=>$cap_n,
            'alumnos_inscritos'=>0,
            'folio_acta'=>null,
            'paralelo_de'=>$mat_o.$gpo_o,
            'exclusivo_carrera'=>$car_p,
            'exclusivo_reticula'=>$ret_p,
            'rfc'=>$rfc,
            'tipo_personal'=>'B',
            'exclusivo'=>'no',
            'entrego'=>0,
            'created_at'=>Carbon::now()
        ]);
        //Ahora, el horario
        for($i=2;$i<=7;$i++){
            $info=DB::table('horarios')->where('periodo',$periodo)->where('materia',$mat_o)
                ->where('grupo',$gpo_o)->where('dia_semana',$i)->get();
            if(!empty($info[0]->hora_inicial)){
                DB::table('horarios')->insert([
                    'periodo'=>$periodo,
                    'rfc'=>$rfc,
                    'tipo_horario'=>'D',
                    'dia_semana'=>$i,
                    'hora_inicial'=>$info[0]->hora_inicial,
                    'hora_final'=>$info[0]->hora_final,
                    'materia'=>$mat_p,
                    'grupo'=>$gpo_p,
                    'aula'=>$info[0]->aula,
                    'actividad'=>null,
                    'consecutivo'=>0,
                    'vigencia_inicio'=>null,
                    'vigencia_fin'=>null,
                    'consecutivo_admvo'=>0,
                    'tipo_personal'=>'B',
                    'created_at'=>Carbon::now()
                ]);
            }
        }
        return view('dep.si');
    }
    public function existentes(){
        $data=Auth::user()->email;
        $carreras=DB::table('permisos_carreras')
            ->where('email',$data)
            ->orderBy('nombre_carrera','asc')
            ->orderBy('reticula','asc')->get();
        $periodos=DB::table('periodos_escolares')->orderBy('periodo','desc')->get();
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        return view('dep.listado')->with(compact('carreras','periodos','periodo'));
    }
    public function listado(Request $request){
        $periodo=$request->get('periodo');
        $carr=$request->get('carrera');
        $data=explode('_',$carr);
        $carrera=$data[0]; $ret=$data[1];
        $ncarrera=DB::table('carreras')->where('carrera',$carrera)->where('reticula',$ret)->get();
        $listado=Db::table('materias_carreras')->where('carrera',$carrera)
            ->where('reticula',$ret)
            ->join('grupos','materias_carreras.materia','=','grupos.materia')
            ->where('grupos.periodo',$periodo)
            ->join('materias','materias_carreras.materia','=','materias.materia')
            ->orderBy('semestre_reticula','asc')
            ->orderBy('nombre_completo_materia','asc')
            ->get();
        return view('dep.listado2')->with(compact('listado','ncarrera','periodo'));
    }
    public function info($periodo,$materia,$grupo){
        $personal=DB::table('grupos')->select('rfc')->where('periodo',$periodo)
            ->where('materia',$materia)->where('grupo',$grupo)->first();
        if(is_null($personal->rfc)){
            $docente="Pendiente por asignar";
        }else{
            $datos_doc=DB::table('personal')->where('rfc',$personal->rfc)->first();
            $docente=$datos_doc->apellidos_empleado." ".$datos_doc->nombre_empleado;
        }
        $nmateria=DB::table('materias')->where('materia',$materia)->first();
        $alumnos=DB::table('seleccion_materias')->where('periodo',$periodo)
            ->where('materia',$materia)
            ->where('grupo',$grupo)
            ->join('alumnos','seleccion_materias.no_de_control','=','alumnos.no_de_control')
            ->orderBy('apellido_paterno','asc')
            ->orderBy('apellido_materno','asc')
            ->orderBy('nombre_alumno','asc')
            ->get();
        return view('dep.informacion')->with(compact('docente','materia','grupo','nmateria','periodo','alumnos'));
    }
    public function acciones(Request $request){
        $materia=$request->get('materia');
        $grupo=$request->get('grupo');
        $periodo=$request->get('periodo');
        $accion=$request->get('accion');
        $nmateria=DB::table('materias')->where('materia',$materia)->first();
        if($accion==1){
            return view('dep.agrupo')->with(compact('materia','grupo','nmateria','periodo'));
        }elseif($accion==2){
            $alumnos=DB::table('seleccion_materias')->where('periodo',$periodo)
                ->where('materia',$materia)
                ->where('grupo',$grupo)
                ->join('alumnos','seleccion_materias.no_de_control','=','alumnos.no_de_control')
                ->orderBy('apellido_paterno','asc')
                ->orderBy('apellido_materno','asc')
                ->orderBy('nombre_alumno','asc')
                ->get();
            return view('dep.bgrupo')->with(compact('materia','grupo','nmateria','alumnos','periodo'));
        }elseif($accion==3){
            //Primero verifico, si tiene inscritos, no puede modificar
            if(DB::table('seleccion_materias')->where('periodo',$periodo)
                    ->where('materia',$materia)->where('grupo',$grupo)->count()>0){
                $mensaje="No puede modificar el horario a una materia que tiene alumnos inscritos";
                return view('dep.no')->with(compact('mensaje'));
                //Ahora, si la materia es paralela, no se puede modificar su horario
            }elseif (DB::table('grupos')->where('periodo',$periodo)
                    ->where('materia',$materia)->where('grupo',$grupo)->whereNotNull('paralelo_de')->count()>0){
                $mensaje="No puede modificar el horario a una materia que es paralela de otra";
                return view('dep.no')->with(compact('mensaje'));
            }else{
                $lunes=DB::table('horarios')->select('hora_inicial','hora_final','aula')
                    ->where('periodo',$periodo)
                    ->where('materia',$materia)->where('grupo',$grupo)
                    ->where('dia_semana',2)
                    ->get();
                $martes=DB::table('horarios')->select('hora_inicial','hora_final','aula')
                    ->where('periodo',$periodo)
                    ->where('materia',$materia)->where('grupo',$grupo)
                    ->where('dia_semana',3)
                    ->get();
                $miercoles=DB::table('horarios')->select('hora_inicial','hora_final','aula')
                    ->where('periodo',$periodo)
                    ->where('materia',$materia)->where('grupo',$grupo)
                    ->where('dia_semana',4)
                    ->get();
                $jueves=DB::table('horarios')->select('hora_inicial','hora_final','aula')
                    ->where('periodo',$periodo)
                    ->where('materia',$materia)->where('grupo',$grupo)
                    ->where('dia_semana',5)
                    ->get();
                $viernes=DB::table('horarios')->select('hora_inicial','hora_final','aula')
                    ->where('periodo',$periodo)
                    ->where('materia',$materia)->where('grupo',$grupo)
                    ->where('dia_semana',6)
                    ->get();
                $sabado=DB::table('horarios')->select('hora_inicial','hora_final','aula')
                    ->where('periodo',$periodo)
                    ->where('materia',$materia)->where('grupo',$grupo)
                    ->where('dia_semana',7)
                    ->get();
                $grupo_existente=DB::table('grupos')->where('periodo',$periodo)
                    ->where('materia',$materia)->where('grupo',$grupo)->get();
                $aulas=DB::table('aulas')->where('estatus','A')->get();
                $mater=DB::table('grupos')->where('periodo',$periodo)
                    ->where('grupos.materia',$materia)->where('grupo',$grupo)
                    ->join('materias_carreras as a1','grupos.materia','=','a1.materia')
                    ->join('materias_carreras as a2','grupos.exclusivo_carrera','=','a2.carrera')
                    ->join('materias_carreras as a3','grupos.exclusivo_reticula','=','a3.reticula')
                    ->join('materias','materias.materia','=','grupos.materia')
                    ->select('nombre_abreviado_materia','a1.creditos_materia')
                    ->first();
                return view('dep.modificar_grupo')->
                with(compact('grupo','materia','mater','aulas','grupo_existente','lunes','martes','miercoles','jueves','viernes','sabado','periodo'));
            }
        }elseif ($accion==4) {
            $cap=DB::table('grupos')->where('periodo',$periodo)->where('materia',$materia)
                ->where('grupo',$grupo)->select('capacidad_grupo')->first();
            return view('dep.capgrupo')->with(compact('materia','grupo','nmateria','periodo','cap'));
        }elseif ($accion==5){
            //Si tiene estudiantes, no puedo borrar
            if(DB::table('seleccion_materias')->where('periodo',$periodo)
                    ->where('materia',$materia)->where('grupo',$grupo)->count()>0){
                $mensaje="La materia cuenta con estudiantes inscritos, por lo que no es posible borrar el grupo";
                return view('dep.no')->with(compact('mensaje'));
            }else{
                //Si tiene grupos paralelas en la misma, no se puede
                $pos_paralela=$materia.$grupo;
                if(DB::table('grupos')->where('periodo',$periodo)->where('paralelo_de',$pos_paralela)->count()>0){
                    $mensaje="La materia tiene grupos paralelos, debe eliminar los dependientes primero para poder eliminar al grupo";
                    return view('dep.no')->with(compact('mensaje'));
                }else{
                    DB::table('grupos')->where('periodo',$periodo)
                        ->where('materia',$materia)->where('grupo',$grupo)->delete();
                    DB::table('horarios')->where('periodo',$periodo)
                        ->where('materia',$materia)->where('grupo',$grupo)->delete();
                    return view('dep.si');
                }
            }
        }
    }
    public function altacontrol(Request $request){
        $data=request()->validate([
            'control'=>'required',
        ],[
            'control.required'=>'Debe indicar un dato para ser buscado'
        ]);
        $control=$request->get('control');
        $global=$request->get('global');
        $materia=$request->get('materia');
        $grupo=$request->get('grupo');
        $periodo=$request->get('periodo');
        //Se verifica primero que es estudiante
        Alumnos::findorFail($control);
        //Determinar si están en permiso de reinscribir
        $permiso=DB::table('periodos_escolares')->where('periodo',$periodo)->select('cierre_seleccion')->first();
        if($permiso->cierre_seleccion=="N"){
            $mensaje="El período de reinscripciones o no ha iniciado o ha concluido";
            return view('dep.no')->with(compact('mensaje'));
        }else{
            //Ahora, si la materia es de su plan de estudios
            if(DB::table('alumnos')->where('no_de_control',$control)
                    ->join('materias_carreras as a1','a1.carrera','=','alumnos.carrera')
                    ->join('materias_carreras as a2','a2.reticula','=','alumnos.reticula')
                    ->where('a1.materia',$materia)
                    ->count()>0
            ){
                //Ver si cuenta con pago registrado
                if(DB::table('avisos_reinscripcion')->where('periodo',$periodo)->where('no_de_control',$control)->count()>0){
                    //No repetir al estudiante en la misma materia
                    if(DB::table('seleccion_materias')->where('periodo',$periodo)
                            ->where('no_de_control',$control)
                            ->where('materia',$materia)
                            ->count()>0){
                        $mensaje="El estudiante ya está inscrito previamente en la materia";
                        return view('dep.no')->with(compact('mensaje'));
                    }else{
                        //No darlo de alta en una materia que ya acreditó. Caso Dra Sara Hndz Coord Sistemas
                        if(DB::table('historia_alumno')->where('no_de_control',$control)
                                ->where('materia',$materia)->where('calificacion','>=',70)
                                ->count()>0){
                            $mensaje="El estudiante ya tiene acreditada la materia";
                            return view('dep.no')->with(compact('mensaje'));
                        }else{
                            //Inscribo
                            if(DB::table('historia_alumno')->where('no_de_control',$control)->where('materia',$materia)->count()>0){
                                $rep="S";
                            }else{
                                $rep="N";
                            }
                            DB::table('seleccion_materias')->insert([
                                'periodo'=>$periodo,
                                'no_de_control'=>$control,
                                'materia'=>$materia,
                                'grupo'=>$grupo,
                                'calificacion'=>null,
                                'tipo_evaluacion'=>null,
                                'repeticion'=>$rep,
                                'nopresento'=>'N',
                                'status_seleccion'=>'C',
                                'fecha_hora_seleccion'=>Carbon::now(),
                                'global'=>$global,
                                'created_at'=>Carbon::now()
                            ]);
                            $quien=Auth::user()->email;
                            DB::table('seleccion_materias_log')->insert([
                                'periodo'=>$periodo,
                                'no_de_control'=>$control,
                                'materia'=>$materia,
                                'grupo'=>$grupo,
                                'movimiento'=>'A',
                                'cuando'=>Carbon::now(),
                                'responsable'=>$quien
                            ]);
                            //Cantidad de inscritos
                            $cant=DB::table('grupos')
                                ->select('alumnos_inscritos','capacidad_grupo')
                                ->where('periodo',$periodo)
                                ->where('materia',$materia)
                                ->where('grupo',$grupo)
                                ->first();
                            $inscritos=$cant->alumnos_inscritos+1;
                            $capacidad=$cant->capacidad_grupo-1;
                            DB::table('grupos')
                                ->where('periodo',$periodo)
                                ->where('materia',$materia)
                                ->where('grupo',$grupo)
                                ->update(['alumnos_inscritos'=>$inscritos,'capacidad_grupo'=>$capacidad]);
                            return redirect()->route('dep_info',['periodo'=>$periodo,'materia'=>$materia,'gpo'=>$grupo]);
                        }
                    }
                }else{
                    $mensaje="No existe pago registrado";
                    return view('dep.no')->with(compact('mensaje'));
                }
            }else{
                $mensaje="La materia no pertenece al plan de estudios del estudiante";
                return view('dep.no')->with(compact('mensaje'));
            }
        }
    }
    public function bajacontrol(Request $request){
        $control=$request->get('control');
        $materia=$request->get('materia');
        $grupo=$request->get('grupo');
        $periodo=$request->get('periodo');
        DB::table('seleccion_materias')
            ->where('periodo',$periodo)
            ->where('no_de_control',$control)
            ->where('materia',$materia)
            ->where('grupo',$grupo)
            ->delete();
        $quien=Auth::user()->email;
        DB::table('seleccion_materias_log')->insert([
            'periodo'=>$periodo,
            'no_de_control'=>$control,
            'materia'=>$materia,
            'grupo'=>$grupo,
            'movimiento'=>'B',
            'cuando'=>Carbon::now(),
            'responsable'=>$quien
        ]);
        //Cantidad de inscritos
        $cant=DB::table('grupos')
            ->select('alumnos_inscritos','capacidad_grupo')
            ->where('periodo',$periodo)
            ->where('materia',$materia)
            ->where('grupo',$grupo)
            ->first();
        $inscritos=$cant->alumnos_inscritos-1;
        $capacidad=$cant->capacidad_grupo+1;
        DB::table('grupos')
            ->where('periodo',$periodo)
            ->where('materia',$materia)
            ->where('grupo',$grupo)
            ->update(['alumnos_inscritos'=>$inscritos,'capacidad_grupo'=>$capacidad]);
        return redirect()->route('dep_info',['periodo'=>$periodo,'materia'=>$materia,'gpo'=>$grupo]);
    }
    public function updatehorario(Request $request){
        $data=request()->validate([
            'grupo'=>'required',
            'capacidad'=>'required',
            'slunes'=>'required_with:elunes',
            'smartes'=>'required_with:emartes',
            'smiercoles'=>'required_with:emiercoles',
            'sjueves'=>'required_with:ejueves',
            'sviernes'=>'required_with:eviernes',
            'ssabado'=>'required_with:esabado',
        ],[
            'grupo.required'=>'Debe indicar la clave del grupo',
            'capacidad.required'=>'Debe indicar la capacidad del grupo',
            'slunes.required_with'=>'Debe indicar la hora de salida para el lunes',
            'smartes.required_with'=>'Debe indicar la hora de salida para el martes',
            'smiercoles.required_with'=>'Debe indicar la hora de salida para el miércoles',
            'sjueves.required_with'=>'Debe indicar la hora de salida para el jueves',
            'sviernes.required_with'=>'Debe indicar la hora de salida para el viernes',
            'ssabado.required_with'=>'Debe indicar la hora de salida para el sabado',
        ]);
        $periodo=$request->get('periodo');
        $materia=$request->get('materia');
        $grupo=$request->get('grupo');
        $creditos=$request->get('creditos');
        $capacidad=$request->get('capacidad');
        $elunes=$request->get('elunes'); if(!empty($elunes)){$elunes=Carbon::parse($elunes);}
        $emartes=$request->get('emartes'); if(!empty($emartes)){$emartes=Carbon::parse($emartes);}
        $emiercoles=$request->get('emiercoles'); if(!empty($emiercoles)){$emiercoles=Carbon::parse($emiercoles);}
        $ejueves=$request->get('ejueves'); if(!empty($ejueves)){$ejueves=Carbon::parse($ejueves);}
        $eviernes=$request->get('eviernes'); if(!empty($eviernes)){$eviernes=Carbon::parse($eviernes);}
        $esabado=$request->get('esabado'); if(!empty($esabado)){$esabado=Carbon::parse($esabado);}
        $slunes=$request->get('slunes'); if(!empty($slunes)){$slunes=Carbon::parse($slunes);}
        $smartes=$request->get('smartes'); if(!empty($smartes)){$smartes=Carbon::parse($smartes);}
        $smiercoles=$request->get('smiercoles'); if(!empty($smiercoles)){$smiercoles=Carbon::parse($smiercoles);}
        $sjueves=$request->get('sjueves'); if(!empty($sjueves)){$sjueves=Carbon::parse($sjueves);}
        $sviernes=$request->get('sviernes'); if(!empty($sviernes)){$sviernes=Carbon::parse($sviernes);}
        $ssabado=$request->get('ssabado'); if(!empty($ssabado)){$ssabado=Carbon::parse($ssabado);}
        $aula_l=$request->get('aula_l');
        $aula_m=$request->get('aula_m');
        $aula_mm=$request->get('aula_mm');
        $aula_j=$request->get('aula_j');
        $aula_v=$request->get('aula_v');
        $aula_s=$request->get('aula_s');
        if(!empty($elunes)){$hl=$elunes->diff($slunes)->format('%h');}else{$hl=0;}
        if(!empty($emartes)){$hm=$emartes->diff($smartes)->format('%h');}else{$hm=0;}
        if(!empty($emiercoles)){$hmm=$emiercoles->diff($smiercoles)->format('%h');}else{$hmm=0;}
        if(!empty($ejueves)){$hj=$ejueves->diff($sjueves)->format('%h');}else{$hj=0;}
        if(!empty($eviernes)){$hv=$eviernes->diff($sviernes)->format('%h');}else{$hv=0;}
        if(!empty($esabado)){$hs=$esabado->diff($ssabado)->format('%h');}else{$hs=0;}
        //Primero, necesito verificar si al momento de mover la materia, no exista un empalme de horas con el docente
        $docente=DB::table('grupos')->select('rfc')->where('periodo',$periodo)
            ->where('materia',$materia)->where('grupo',$grupo)->first();
        if(!empty($docente->rfc)){
            $bandera=1;
        }else {
            $bandera = 0;
        }
        //Después, que el salón esté libre (eso lo hace el trigger)
        $total_horas=$hl+$hm+$hmm+$hj+$hv+$hs;
        if($total_horas==$creditos){
            //Que no sea un grupo repetido
            if(!empty($elunes)){
                try{
                    DB::table('horarios')->where('periodo',$periodo)
                        ->where('materia',$materia)->where('grupo',$grupo)->where('dia_semana',2)
                        ->update([
                            'hora_inicial'=>$elunes,
                            'hora_final'=>$slunes,
                            'aula'=>$aula_l,
                            'updated_at'=>Carbon::now()
                        ]);
                }catch (QueryException $e){
                    $mensaje=$bandera==1?"El docente tiene materia a la hora señalada":"El aula se encuentra ocupada el día lunes";
                    return view('dep.no')->with(compact('mensaje'));
                }
                //Si tiene paralela, también se actualiza
            }
            if(!empty($emartes)){
                try{
                    DB::table('horarios')->where('periodo',$periodo)
                        ->where('materia',$materia)->where('grupo',$grupo)->where('dia_semana',3)
                        ->update([
                            'hora_inicial'=>$emartes,
                            'hora_final'=>$smartes,
                            'aula'=>$aula_m,
                            'updated_at'=>Carbon::now()
                        ]);
                }catch (QueryException $e){
                    $mensaje=$bandera==1?"El docente tiene materia a la hora señalada":"El aula se encuentra ocupada el día martes";
                    return view('dep.no')->with(compact('mensaje'));
                }
                //Si tiene paralela, también se actualiza
            }
            if(!empty($emiercoles)){
                try{
                    DB::table('horarios')->where('periodo',$periodo)
                        ->where('materia',$materia)->where('grupo',$grupo)->where('dia_semana',4)
                        ->update([
                            'hora_inicial'=>$emiercoles,
                            'hora_final'=>$smiercoles,
                            'aula'=>$aula_mm,
                            'updated_at'=>Carbon::now()
                        ]);
                }catch (QueryException $e){
                    $mensaje=$bandera==1?"El docente tiene materia a la hora señalada":"El aula se encuentra ocupada el día miercoles";
                    return view('dep.no')->with(compact('mensaje'));
                }
                //Si tiene paralela, también se actualiza
            }
            if(!empty($ejueves)){
                try{
                    DB::table('horarios')->where('periodo',$periodo)
                        ->where('materia',$materia)->where('grupo',$grupo)->where('dia_semana',5)
                        ->update([
                            'hora_inicial'=>$ejueves,
                            'hora_final'=>$sjueves,
                            'aula'=>$aula_j,
                            'updated_at'=>Carbon::now()
                        ]);
                }catch (QueryException $e){
                    $mensaje=$bandera==1?"El docente tiene materia a la hora señalada":"El aula se encuentra ocupada el día jueves";
                    return view('dep.no')->with(compact('mensaje'));
                }
                //Si tiene paralela, también se actualiza
            }
            if(!empty($eviernes)){
                try{
                    DB::table('horarios')->where('periodo',$periodo)
                        ->where('materia',$materia)->where('grupo',$grupo)->where('dia_semana',6)
                        ->update([
                            'hora_inicial'=>$eviernes,
                            'hora_final'=>$sviernes,
                            'aula'=>$aula_v,
                            'updated_at'=>Carbon::now()
                        ]);
                }catch (QueryException $e){
                    $mensaje=$bandera==1?"El docente tiene materia a la hora señalada":"El aula se encuentra ocupada el día viernes";
                    return view('dep.no')->with(compact('mensaje'));
                }
                //Si tiene paralela, también se actualiza
            }
            if(!empty($esabado)){
                try{
                    DB::table('horarios')->where('periodo',$periodo)
                        ->where('materia',$materia)->where('grupo',$grupo)->where('dia_semana',7)
                        ->update([
                            'hora_inicial'=>$esabado,
                            'hora_final'=>$ssabado,
                            'aula'=>$aula_s,
                            'updated_at'=>Carbon::now()
                        ]);
                }catch (QueryException $e){
                    $mensaje=$bandera==1?"El docente tiene materia a la hora señalada":"El aula se encuentra ocupada el día sabado";
                    return view('dep.no')->with(compact('mensaje'));
                }
                //Si tiene paralela, también se actualiza
            }
            DB::table('grupos')->where('periodo',$periodo)
                ->where('materia',$materia)->where('grupo',$grupo)->update([
                    'capacidad_grupo'=>$capacidad,
                    'created_at'=>Carbon::now()
                ]);
            return view('dep.si');
        }else{
            $mensaje="No se pudo realizar la acción porque no concuerda el número de horas a impartir contra las que debe tener la materia";
            return view('dep.no')->with(compact('mensaje'));
        }
    }
    public function buscar(){
        return view('dep.busqueda');
    }
    public function busqueda(Request $request){
        $data=request()->validate([
            'control'=>'required',
        ],[
            'control.required'=>'Debe indicar un dato para ser buscado'
        ]);
        $id=$request->get('control');
        $tbusqueda=$request->get('tbusqueda');
        if($tbusqueda=="1"){
            $alumno=Alumnos::findOrfail($id);
            $datos=AlumnosGenerales::findOrfail($id);
            $ncarrera=Db::table('carreras')->select('nombre_carrera')
                ->where('carrera',$alumno->carrera)
                ->where('reticula',$alumno->reticula)
                ->get();
            $periodo=$this->periodo();
            $periodos=DB::table('periodos_escolares')
                ->orderBy('periodo','desc')
                ->get();
            $espe=DB::table('especialidades')->where('especialidad',$alumno->especialidad)
                ->where('carrera',$alumno->carrera)->where('reticula',$alumno->reticula)->first();
            if(empty($espe)){
                $especialidad="POR ASIGNAR";
            }else{
                $especialidad=$espe->nombre_especialidad;
            }
            $estatus=Db::table('estatus_alumno')->where('estatus',$alumno->estatus_alumno)->get();
            return view('dep.datos')->
            with(compact('alumno','ncarrera','datos','id','periodo','periodos','estatus','especialidad'));
        }elseif ($tbusqueda=='2'){
            $arroja=Alumnos::where('apellido_paterno',strtoupper($id))
                ->orWhere('apellido_materno',strtoupper($id))
                ->orWhere('nombre_alumno',strtoupper($id))
                ->orderBY('apellido_paterno')
                ->orderBy('apellido_materno')
                ->orderBy('nombre_alumno')
                ->get();
            return view('dep.datos2')->with(compact('arroja'));
        }
    }
    public function accion2(Request $request){
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        $control=$request->control;
        $accion=$request->accion;
        $alumno=Alumnos::findOrfail($control);
        $ncarrera=Db::table('carreras')->select('nombre_carrera')
            ->where('carrera',$alumno->carrera)
            ->where('reticula',$alumno->reticula)
            ->get();
        $estatus=Db::table('estatus_alumno')->where('estatus',$alumno->estatus_alumno)->get();
        if($accion==1) {
            $calificaciones = $this->kardex($control);
            $nperiodos = $this->nperiodo($control);
            $espe=DB::table('especialidades')->where('especialidad',$alumno->especialidad)
                ->where('carrera',$alumno->carrera)->where('reticula',$alumno->reticula)->first();
            if(empty($espe)){
                $especialidad="POR ASIGNAR";
            }else{
                $especialidad=$espe->nombre_especialidad;
            }
            return view('dep.kardex')
                ->with(compact('alumno', 'calificaciones', 'estatus', 'ncarrera', 'nperiodos','especialidad','control'));
        }elseif($accion==2){
            $historial=$this->reticula($control);
            return view('dep.reticula')->with(compact('alumno','historial'));
        }elseif ($accion==3){
            if(DB::table('seleccion_materias')
                    ->where('no_de_control',$control)
                    ->where('periodo',$periodo_actual[0]->periodo)
                    ->count()>0){
                $datos_horario=DB::select("select * from pac_horario('$control','$periodo')");
                $nombre_periodo=DB::table('periodos_escolares')->where('periodo',$periodo)->get();
                return view('dep.horario')->with(compact('alumno','datos_horario','nombre_periodo','periodo_actual'));
            }else{
                $mensaje="NO CUENTA CON CARGA ACADÉMICA ASIGNADA";
                return view('dep.no')->with(compact('mensaje'));
            }
        }elseif ($accion==4){
            if(DB::table('avisos_reinscripcion')->where('periodo',$periodo)->where('no_de_control',$control)->count()>0){
                DB::table('avisos_reinscripcion')->where('periodo',$periodo)
                    ->where('no_de_control',$control)->update([
                        'autoriza_escolar'=>'S',
                        'recibo_pago'=>'1',
                        'fecha_hora_seleccion'=>Carbon::now(),
                        'encuesto'=>'S',
                        'updated_at'=>Carbon::now()
                    ]);
            }else{
                DB::table('avisos_reinscripcion')->insert([
                    'periodo'=>$periodo,
                    'no_de_control'=>$control,
                    'autoriza_escolar'=>'S',
                    'recibo_pago'=>'1',
                    'fecha_recibo'=>null,
                    'cuenta_pago'=>null,
                    'fecha_hora_seleccion'=>Carbon::now(),
                    'lugar_seleccion'=>null,
                    'fecha_hora_pago'=>null,
                    'lugar_pago'=>null,
                    'adeuda_escolar'=>null,
                    'adeuda_biblioteca'=>null,
                    'adeuda_financieros'=>null,
                    'otro_mensaje'=>null,
                    'baja'=>null,
                    'motivo_aviso_baja'=>null,
                    'egresa'=>null,
                    'encuesto'=>'S',
                    'vobo_adelanta_sel'=>null,
                    'regular'=>null,
                    'indice_reprobacion'=>0,
                    'creditos_autorizados'=>0,
                    'estatus_reinscripcion'=>null,
                    'semestre'=>0,
                    'promedio'=>0,
                    'adeudo_especial'=>'N',
                    'promedio_acumulado'=>null,
                    'proareas'=>null,
                    'created_at'=>Carbon::now()
                ]);
            }
            return view('dep.inicio');
        }
    }
    public function capgrupo(Request $request){
        $data=request()->validate([
            'capacidad'=>'required',
        ],[
            'capacidad.required'=>'Debe indicar la capacidad para la materia'
        ]);
        $periodo=$request->get('periodo');
        $materia=$request->get('materia');
        $grupo=$request->get('grupo');
        $cap=$request->get('capacidad');
        $previa_cap=$request->get('cap_old');
        if($cap<=0){
            $mensaje="La materia no puede tener cantidades negativas";
            return view('dep.no')->with(compact('mensaje'));
        }elseif ($cap<$previa_cap){
            $mensaje="No se llevó a cabo la actualización debido a que la capacidad solicitada es
            menor a la actual";
            return view('dep.no')->with(compact('mensaje'));
        }else{
            DB::table('grupos')->where('periodo',$periodo)
                ->where('materia',$materia)->where('grupo',$grupo)
                ->update([
                    'capacidad_grupo'=>$cap
                ]);
            return view('dep.si');
        }
    }
    public function prepoblacion(){
        $periodos=DB::table('periodos_escolares')->orderBy('periodo','desc')->get();
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        return view('dep.prepoblacion')->with(compact('periodos','periodo'));
    }
    public function poblacion(Request $request){
        $periodo=$request->get('periodo');
        $inscritos=$this->inscritos($periodo);
        $nperiodo=DB::table('periodos_escolares')->where('periodo',$periodo)->first();
        return view('dep.poblacion')->with(compact('inscritos','periodo','nperiodo'));
    }
    public function pobxcarrera($periodo,$carrera,$reticula){
        $nperiodo=DB::table('periodos_escolares')->where('periodo',$periodo)->first();
        $ncarrera=DB::table('carreras')->where('carrera',$carrera)->where('reticula',15)->first();
        $cantidad=DB::table('seleccion_materias')->where('periodo',$periodo)
            ->join('alumnos','alumnos.no_de_control','=','seleccion_materias.no_de_control')
            ->where('carrera',$carrera)
            ->where('reticula',$reticula)
            ->selectRaw('COUNT(DISTINCT(seleccion_materias.no_de_control)) AS inscritos, semestre')
            ->groupByRaw('semestre')
            ->get();
        $pob_masc=DB::table('seleccion_materias')->where('periodo',$periodo)
            ->join('alumnos','alumnos.no_de_control','=','seleccion_materias.no_de_control')
            ->where('carrera',$carrera)
            ->where('reticula',$reticula)
            ->where('sexo','M')
            ->selectRaw('COUNT(DISTINCT(seleccion_materias.no_de_control)) AS inscritos, semestre')
            ->groupByRaw('semestre')
            ->get();
        $pob_fem=DB::table('seleccion_materias')->where('periodo',$periodo)
            ->join('alumnos','alumnos.no_de_control','=','seleccion_materias.no_de_control')
            ->where('carrera',$carrera)
            ->where('reticula',$reticula)
            ->where('sexo','F')
            ->selectRaw('COUNT(DISTINCT(seleccion_materias.no_de_control)) AS inscritos, semestre')
            ->groupByRaw('semestre')
            ->get();
        return view('dep.poblacion2')->with(compact('cantidad','pob_masc','pob_fem','ncarrera','reticula','nperiodo'));
    }
    public function pobxaulas(){
        $periodos=DB::table('periodos_escolares')->orderBy('periodo','desc')->get();
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        $aulas=DB::table('aulas')->where('estatus','A')->get();
        return view('dep.aulas')->with(compact('aulas','periodos','periodo'));
    }
    public function pobxaulas2(Request $request){
        $aula=$request->get('salon');
        $periodo=$request->get('periodo');
        $nperiodo=DB::table('periodos_escolares')->where('periodo',$periodo)->first();
        $lunes=DB::table('horarios')->where('periodo',$periodo)->where('dia_semana',2)
            ->where('aula',$aula)
            ->join('materias','materias.materia','=','horarios.materia')
            ->join('materias_carreras','materias_carreras.materia','=','materias.materia')
            ->join('carreras','carreras.carrera','=','materias_carreras.carrera')
            ->select('hora_inicial','hora_final','nombre_abreviado_materia','horarios.materia','grupo','rfc')
            ->distinct()
            ->get();
        $martes=DB::table('horarios')->where('periodo',$periodo)->where('dia_semana',3)
            ->where('aula',$aula)
            ->join('materias','materias.materia','=','horarios.materia')
            ->join('materias_carreras','materias_carreras.materia','=','materias.materia')
            ->join('carreras','carreras.carrera','=','materias_carreras.carrera')
            ->select('hora_inicial','hora_final','nombre_abreviado_materia','horarios.materia','grupo','rfc')
            ->distinct()
            ->get();
        $miercoles=DB::table('horarios')->where('periodo',$periodo)->where('dia_semana',4)
            ->where('aula',$aula)
            ->join('materias','materias.materia','=','horarios.materia')
            ->join('materias_carreras','materias_carreras.materia','=','materias.materia')
            ->join('carreras','carreras.carrera','=','materias_carreras.carrera')
            ->select('hora_inicial','hora_final','nombre_abreviado_materia','horarios.materia','grupo','rfc')
            ->distinct()
            ->get();
        $jueves=DB::table('horarios')->where('periodo',$periodo)->where('dia_semana',5)
            ->where('aula',$aula)
            ->join('materias','materias.materia','=','horarios.materia')
            ->join('materias_carreras','materias_carreras.materia','=','materias.materia')
            ->join('carreras','carreras.carrera','=','materias_carreras.carrera')
            ->select('hora_inicial','hora_final','nombre_abreviado_materia','horarios.materia','grupo','rfc')
            ->distinct()
            ->get();
        $viernes=DB::table('horarios')->where('periodo',$periodo)->where('dia_semana',6)
            ->where('aula',$aula)
            ->join('materias','materias.materia','=','horarios.materia')
            ->join('materias_carreras','materias_carreras.materia','=','materias.materia')
            ->join('carreras','carreras.carrera','=','materias_carreras.carrera')
            ->select('hora_inicial','hora_final','nombre_abreviado_materia','horarios.materia','grupo','rfc')
            ->distinct()
            ->get();
        $sabado=DB::table('horarios')->where('periodo',$periodo)->where('dia_semana',7)
            ->where('aula',$aula)
            ->join('materias','materias.materia','=','horarios.materia')
            ->join('materias_carreras','materias_carreras.materia','=','materias.materia')
            ->join('carreras','carreras.carrera','=','materias_carreras.carrera')
            ->select('hora_inicial','hora_final','nombre_abreviado_materia','horarios.materia','grupo','rfc')
            ->distinct()
            ->get();
        return view('dep.aulas2')->with(compact('nperiodo','aula','lunes','martes','miercoles','jueves','viernes','sabado','periodo'));
    }
    public function contrasenia(){
        return view('dep.contrasenia');
    }
    public function ccontrasenia(Request $request){
        $data=request()->validate([
            'contra'=>'required|required_with:verifica|same:verifica',
            'verifica'=>'required'
        ],[
            'contra.required'=>'Debe escribir la nueva contraseña',
            'contra.required_with'=>'Debe confirmar la contraseña',
            'contra.same'=>'No concuerda con la verificacion',
            'verifica.required'=>'Debe confirmar la nueva contraseña'
        ]);
        $ncontra=bcrypt($request->get('contra'));
        $data=Auth::user()->email;
        DB::table('users')->where('email',$data)->update([
            'password'=>$ncontra,
            'updated_at'=>Carbon::now()
        ]);
        return view('dep.inicio');
    }
    public function altadocente(Request $request){
        $materia=$request->get('materia');
        $grupo=$request->get('grupo');
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        //Si la materia es paralela, no se puede modificar, solo la base
        if(DB::table('grupos')->where('periodo',$periodo)
                ->where('materia',$materia)->where('grupo',$grupo)->whereNotNull('paralelo_de')->count()>0){
            $mensaje="No puede asignarle docente a una materia paralela";
            return view('dep.no')->with(compact('mensaje'));
        }else{
            $docente=$request->get('docente');
            if($docente!='999'){
                $dias=DB::table('horarios')->select('dia_semana')
                    ->where('periodo',$periodo)->where('materia',$materia)
                    ->where('grupo',$grupo)->get();
                $mensaje="";
                $suma=0;
                foreach ($dias as $dia){
                    $pcruce=$this->cruce($periodo,$materia,$grupo,$docente,$dia->dia_semana);
                    switch ($dia->dia_semana){
                        case 2: $ndia="lunes"; break;
                        case 3: $ndia="martes"; break;
                        case 4: $ndia="miércoles"; break;
                        case 5: $ndia="jueves"; break;
                        case 6: $ndia="viernes"; break;
                        case 7: $ndia="sábado"; break;
                    }
                    if($pcruce){
                        $mensaje.="El docente tiene cruce de horario el día ".$ndia."\n";
                        $suma++;
                    }
                }
                if($suma>0){
                    return view('dep.no')->with(compact('mensaje'));
                }else{
                    DB::table('horarios')->where('periodo',$periodo)->where('materia',$materia)
                        ->where('grupo',$grupo)->update(['rfc'=>$docente,'updated_at'=>Carbon::now()]);
                    DB::table('grupos')->where('periodo',$periodo)
                        ->where('materia',$materia)->where('grupo',$grupo)
                        ->update(['rfc'=>$docente,'updated_at'=>Carbon::now()]);
                    //Si tiene materia paralela, se le debe asignar el docente también
                    $pparala=$materia.$grupo;
                    if(DB::table('grupos')->where('periodo',$periodo)->where('paralelo_de',$pparala)->count()>0){
                        $datos=DB::table('grupos')->where('periodo',$periodo)->where('paralelo_de',$pparala)
                            ->select('materia','grupo')->get();
                        foreach ($datos as $valores){
                            $mat_p=$datos[0]->materia;
                            $gpo_p=$datos[0]->gpo;
                            DB::table('horarios')->where('periodo',$periodo)->where('materia',$mat_p)
                                ->where('grupo',$gpo_p)->update(['rfc'=>$docente,'updated_at'=>Carbon::now()]);
                            DB::table('grupos')->where('periodo',$periodo)
                                ->where('materia',$mat_p)->where('grupo',$gpo_p)
                                ->update(['rfc'=>$docente,'updated_at'=>Carbon::now()]);
                        }
                    }
                    return redirect()->route('dep_info',['materia'=>$materia,'gpo'=>$grupo]);
                }
            }else{
                DB::table('horarios')->where('periodo',$periodo)->where('materia',$materia)
                    ->where('grupo',$grupo)->update(['rfc'=>null,'updated_at'=>Carbon::now()]);
                DB::table('grupos')->where('periodo',$periodo)
                    ->where('materia',$materia)->where('grupo',$grupo)
                    ->update(['rfc'=>null,'updated_at'=>Carbon::now()]);
                //Si tiene materia paralela, se le debe asignar el docente también
                $pparala=$materia.$grupo;
                if(DB::table('grupos')->where('periodo',$periodo)->where('paralelo_de',$pparala)->count()>0){
                    $datos=DB::table('grupos')->where('periodo',$periodo)->where('paralelo_de',$pparala)
                        ->select('materia','grupo')->get();
                    foreach ($datos as $valores){
                        $mat_p=$datos[0]->materia;
                        $gpo_p=$datos[0]->gpo;
                        DB::table('horarios')->where('periodo',$periodo)->where('materia',$mat_p)
                            ->where('grupo',$gpo_p)->update(['rfc'=>null,'updated_at'=>Carbon::now()]);
                        DB::table('grupos')->where('periodo',$periodo)
                            ->where('materia',$mat_p)->where('grupo',$gpo_p)
                            ->update(['rfc'=>null,'updated_at'=>Carbon::now()]);
                    }
                }
                return redirect()->route('dep_info',['materia'=>$materia,'gpo'=>$grupo]);
            }
        }
    }
    public function psemestre(){
        return view('dep.psemestre');
    }
    public function psemestre2(Request $request){
        $data=request()->validate([
            'control'=>'required'
        ],[
            'control.required'=>'Debe indicar el número de control'
        ]);
        $control=$request->get('control');
        $alumno=Alumnos::findOrfail($control);
        $ncarrera=DB::table('carreras')->where('carrera',$alumno->carrera)
            ->where('reticula',$alumno->reticula)->first();
        $data=Auth::user()->email;
        $carreras=DB::table('permisos_carreras')
            ->where('email',$data)
            ->orderBy('nombre_carrera','asc')
            ->orderBy('reticula','asc')->get();
        return view('dep.psemestre2')->with(compact('carreras','control','alumno','ncarrera','carreras'));
    }
    public function psemestrecambio(Request $request){
        $data=request()->validate([
            'grupo'=>'required'
        ],[
            'grupo.required'=>'Debe indicar el grupo donde tendrá la carga académica'
        ]);
        $control=$request->get('control');
        $destino=$request->get('destino');
        $info=explode("_",$destino);
        $carreran=$info[0]; $retn=$info[1];
        $grupo=trim($request->get('grupo'));
        $alumno=Alumnos::findOrfail($control);
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        //Primero, si está en tiempo
        if(DB::table('periodos_escolares')->where('periodo',$periodo)->where('ccarrera',1)->count()>0){
            //Si es de primer semestre
            if(($alumno->semestre==1)&&($alumno->periodo_ingreso_it==$periodo)){
                //Que existan materias en el grupo que se menciona
                $materias=DB::table('materias_carreras')->where('carrera',$carreran)
                    ->where('reticula',$retn)->where('semestre_reticula',1)
                    ->select('materia')
                    ->get();
                $contador=0;
                foreach ($materias as $materia){
                    if(DB::table('grupos')->where('periodo',$periodo)->where('materia',$materia->materia)
                        ->where('grupo',$grupo)->count()>0){
                        $contador++;
                    }
                }
                if($contador==0){
                    $mensaje="No se pudieron localizar materias de primer semestre para el grupo señalado";
                    return view('dep.no')->with(compact('mensaje'));
                }else{
                    //Empieza el cambio
                    //Eliminar todas las materias que tenga al momento
                    DB::table('seleccion_materias')->where('periodo',$periodo)
                        ->where('no_de_control',$control)->delete();
                    //Cambiar carrera
                    DB::table('alumnos')->where('no_de_control',$control)->update([
                        'carrera'=>$carreran,
                        'reticula'=>$retn,
                        'updated_at'=>Carbon::now()
                    ]);
                    //Ahora, a inscribirse
                    foreach ($materias as $materia){
                        DB::table('seleccion_materias')->insert([
                            'periodo'=>$periodo,
                            'no_de_control'=>$control,
                            'materia'=>$materia->materia,
                            'grupo'=>$grupo,
                            'calificacion'=>null,
                            'tipo_evaluacion'=>null,
                            'repeticion'=>null,
                            'nopresento'=>null,
                            'status_seleccion'=>'C',
                            'fecha_hora_seleccion'=>Carbon::now(),
                            'global'=>'N',
                            'created_at'=>Carbon::now(),
                            'updated_at'=>null
                        ]);
                    }
                    return view('dep.si');
                }
            }else{
                $mensaje="El estudiante no es de primer semestre, por lo que no es posible realizar el movimiento";
                return view('dep.no')->with(compact('mensaje'));
            }
        }else{
            $mensaje="La autorización de cambio de carrera ha terminado";
            return view('dep.no')->with(compact('mensaje'));
        }
    }
}
