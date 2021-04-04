<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AcademicosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(){
        return view('acad.inicio');
    }
    public function periodo(){
        $periodo_actual=Db::Select('select periodo from pac_periodo_actual()');
        return $periodo_actual;
    }
    public function cruce($periodo,$materia,$grupo,$docente,$dia){
        $verifica=DB::select("select cruce from cruce_horario('$periodo','$materia','$grupo','$docente','$dia')");
        return $verifica;
    }
    public function inscritos($periodo){
        $data=DB::select("select * from pac_poblacion('$periodo')");
        return $data;
    }
    public function existentes(){
        $carreras=DB::table('carreras')->orderBy('nombre_carrera','asc')
            ->orderBy('reticula','asc')->get();
        $periodos=DB::table('periodos_escolares')->orderBy('periodo','desc')->get();
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        return view('acad.listado')->with(compact('carreras','periodos','periodo'));
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
        return view('acad.listado2')->with(compact('listado','ncarrera','periodo'));
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
        return view('acad.informacion')->with(compact('docente','materia','grupo','nmateria','periodo','alumnos'));
    }
    public function acciones(Request $request){
        $materia=$request->get('materia');
        $grupo=$request->get('grupo');
        $periodo=$request->get('periodo');
        $accion=$request->get('accion');
        $nmateria=DB::table('materias')->where('materia',$materia)->first();
        if($accion==1){
            $personal=DB::table('personal')->where('inactivo_rc','N')->where('nombramiento','D')
                ->where('status_empleado','2')
                ->orderBy('apellidos_empleado','asc')->orderBy('nombre_empleado','asc')->get();
            return view('acad.alta_docente')->with(compact('materia','grupo','nmateria','personal','periodo'));
        }
    }
    public function altadocente(Request $request){
        $materia=$request->get('materia');
        $grupo=$request->get('grupo');
        $periodo=$request->get('periodo');
        //Si la materia es paralela, no se puede modificar, solo la base
        if(DB::table('grupos')->where('periodo',$periodo)
                ->where('materia',$materia)->where('grupo',$grupo)->whereNotNull('paralelo_de')->count()>0){
            $mensaje="No puede asignarle docente a una materia paralela";
            return view('acad.no')->with(compact('mensaje'));
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
                    if($pcruce==1){
                        $mensaje.="El docente tiene cruce de horario el día ".$ndia."\n";
                        $suma++;
                    }
                }
                if($suma>0){
                    return view('acad.no')->with(compact('mensaje'));
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
                            $gpo_p=$datos[0]->grupo;
                            DB::table('horarios')->where('periodo',$periodo)->where('materia',$mat_p)
                                ->where('grupo',$gpo_p)->update(['rfc'=>$docente,'updated_at'=>Carbon::now()]);
                            DB::table('grupos')->where('periodo',$periodo)
                                ->where('materia',$mat_p)->where('grupo',$gpo_p)
                                ->update(['rfc'=>$docente,'updated_at'=>Carbon::now()]);
                        }
                    }
                    return redirect()->route('acad_info',['periodo'=>$periodo,'materia'=>$materia,'gpo'=>$grupo]);
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
                        $gpo_p=$datos[0]->grupo;
                        DB::table('horarios')->where('periodo',$periodo)->where('materia',$mat_p)
                            ->where('grupo',$gpo_p)->update(['rfc'=>null,'updated_at'=>Carbon::now()]);
                        DB::table('grupos')->where('periodo',$periodo)
                            ->where('materia',$mat_p)->where('grupo',$gpo_p)
                            ->update(['rfc'=>null,'updated_at'=>Carbon::now()]);
                    }
                }
                return redirect()->route('acad_info',['periodo'=>$periodo,'materia'=>$materia,'gpo'=>$grupo]);
            }
        }
    }
    public function prepoblacion(){
        $periodos=DB::table('periodos_escolares')->orderBy('periodo','desc')->get();
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        return view('acad.prepoblacion')->with(compact('periodos','periodo'));
    }
    public function poblacion(Request $request){
        $periodo=$request->get('periodo');
        $inscritos=$this->inscritos($periodo);
        $nperiodo=DB::table('periodos_escolares')->where('periodo',$periodo)->first();
        return view('acad.poblacion')->with(compact('inscritos','periodo','nperiodo'));
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
        return view('acad.poblacion2')->with(compact('cantidad','pob_masc','pob_fem','ncarrera','reticula','nperiodo'));
    }
    public function pobxaulas(){
        $periodos=DB::table('periodos_escolares')->orderBy('periodo','desc')->get();
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        $aulas=DB::table('aulas')->where('estatus','A')->get();
        return view('acad.aulas')->with(compact('aulas','periodos','periodo'));
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
        return view('acad.aulas2')->with(compact('nperiodo','aula','lunes','martes','miercoles','jueves','viernes','sabado','periodo'));
    }
    public function predocentes(){
        $personal=DB::table('personal')->where('inactivo_rc','N')->where('nombramiento','D')
            ->where('status_empleado','2')
            ->orderBy('apellidos_empleado','asc')->orderBy('nombre_empleado','asc')->get();
        $periodos=DB::table('periodos_escolares')->orderBy('periodo','desc')->get();
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        return view('acad.docentes')->with(compact('personal','periodo','periodos'));
    }
    public function docente(Request $request){
        $rfc=$request->get('rfc');
        $periodo=$request->get('periodo');
        $nperiodo=DB::table('periodos_escolares')->where('periodo',$periodo)->select('identificacion_larga')->first();
        $info=DB::table('grupos')->where('periodo',$periodo)->where('rfc',$rfc)
            ->whereNull('paralelo_de')
            ->join('materias_carreras as mc','mc.materia','=','grupos.materia')
            ->join('materias','mc.materia','=','materias.materia')
            ->select('grupos.materia','grupo','nombre_abreviado_materia')
            ->distinct('grupos.materia')
            ->get();
        $admin=DB::table('horario_administrativo')->where('periodo',$periodo)->where('rfc',$rfc)
            ->join('puestos','horario_administrativo.descripcion_horario','=','puestos.clave_puesto')
            ->distinct('consecutivo_admvo')
            ->select('consecutivo_admvo','descripcion_puesto')->get();
        $apoyo=DB::table('apoyo_docencia')->where('periodo',$periodo)->where('rfc',$rfc)
            ->join('actividades_apoyo','apoyo_docencia.actividad','=','actividades_apoyo.actividad')
            ->distinct('consecutivo')
            ->get();
        return view('acad.horario')->with(compact('rfc','periodo','info','nperiodo','admin','apoyo'));
    }
    public function otroshorarios(){
        $periodos=DB::table('periodos_escolares')->orderBy('periodo','desc')->get();
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        $personal=DB::table('personal')->where('inactivo_rc','N')->where('nombramiento','D')
            ->where('status_empleado','2')
            ->orderBy('apellidos_empleado','asc')->orderBy('nombre_empleado','asc')->get();
        return view('acad.otroshorarios')->with(compact('periodo','periodos','personal'));
    }
    public function otroshorariosaccion(Request $request){
        $periodo=$request->get('periodo');
        $accion=$request->get('accion');
        $rfc=$request->get('rfc');
        $personal=DB::table('personal')->where('rfc',$rfc)->select('apellidos_empleado','nombre_empleado')->first();
        $puestos=DB::table('puestos')->get();
        $nperiodo=DB::table('periodos_escolares')->where('periodo',$periodo)->select('identificacion_larga')->first();
        $info=DB::table('grupos')->where('periodo',$periodo)->where('rfc',$rfc)
            ->whereNull('paralelo_de')
            ->join('materias_carreras as mc','mc.materia','=','grupos.materia')
            ->join('materias','mc.materia','=','materias.materia')
            ->select('grupos.materia','grupo','nombre_abreviado_materia')
            ->distinct('grupos.materia')
            ->get();
        $admin=DB::table('horario_administrativo')->where('periodo',$periodo)->where('rfc',$rfc)
            ->join('puestos','horario_administrativo.descripcion_horario','=','puestos.clave_puesto')
            ->distinct('consecutivo_admvo')
            ->select('consecutivo_admvo','descripcion_puesto')->get();
        $apoyo=DB::table('apoyo_docencia')->where('periodo',$periodo)->where('rfc',$rfc)
            ->join('actividades_apoyo','apoyo_docencia.actividad','=','actividades_apoyo.actividad')
            ->distinct('consecutivo')
            ->get();
        if($accion==1){
            return view('acad.alta_hadmvo')->with(compact('periodo','puestos','rfc','info','nperiodo','admin'));
        }elseif($accion==2){
            return view('acad.modificar_hadmvo')->with(compact('periodo','puestos','rfc','nperiodo','admin'));
        }elseif($accion==3){
            $apoyos=DB::table('actividades_apoyo')->get();
            return view('acad.alta_apoyo')->with(compact('periodo','puestos','rfc','nperiodo','admin','apoyos','info'));
        }elseif($accion==4){
            return view('acad.modificar_hapoyo')->with(compact('periodo','rfc','nperiodo','apoyo'));
        }elseif ($accion==5){
            return view('acad.alta_obs')->with(compact('periodo','rfc','personal'));
        }elseif ($accion==6){
            if(DB::table('horario_observaciones')->where('periodo',$periodo)->where('rfc',$rfc)->count()>0){
                $obs=DB::table('horario_observaciones')->where('periodo',$periodo)->where('rfc',$rfc)
                    ->select('observaciones')->first();
                return view('acad.modificar_obs')->with(compact('periodo','rfc','personal','obs'));
            }else{
                $mensaje="El docente no cuenta con observaciones en el horario, por lo que no es posible modificar nada";
                return view('acad.no')->with(compact('mensaje'));
            }


        }
    }
    public function procesaadmvoalta(Request $request){
        $data=request()->validate([
            'slunes'=>'required_with:elunes',
            'smartes'=>'required_with:emartes',
            'smiercoles'=>'required_with:emiercoles',
            'sjueves'=>'required_with:ejueves',
            'sviernes'=>'required_with:eviernes',
            'ssabado'=>'required_with:esabado',
        ],[
            'slunes.required_with'=>'Debe indicar la hora de salida para el lunes',
            'smartes.required_with'=>'Debe indicar la hora de salida para el martes',
            'smiercoles.required_with'=>'Debe indicar la hora de salida para el miércoles',
            'sjueves.required_with'=>'Debe indicar la hora de salida para el jueves',
            'sviernes.required_with'=>'Debe indicar la hora de salida para el viernes',
            'ssabado.required_with'=>'Debe indicar la hora de salida para el sabado',
        ]);
        $periodo=$request->get('periodo');
        $actividad=$request->get('puesto');
        $rfc=$request->get('rfc');
        $lun=$request->get('hl');
        $mar=$request->get('hm');
        $mie=$request->get('hmm');
        $jue=$request->get('hj');
        $vie=$request->get('hv');
        $sab=$request->get('hs');
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

        if(!empty($elunes)){$hl=$elunes->diff($slunes)->format('%h');}else{$hl=0;}
        if(!empty($emartes)){$hm=$emartes->diff($smartes)->format('%h');}else{$hm=0;}
        if(!empty($emiercoles)){$hmm=$emiercoles->diff($smiercoles)->format('%h');}else{$hmm=0;}
        if(!empty($ejueves)){$hj=$ejueves->diff($sjueves)->format('%h');}else{$hj=0;}
        if(!empty($eviernes)){$hv=$eviernes->diff($sviernes)->format('%h');}else{$hv=0;}
        if(!empty($esabado)){$hs=$esabado->diff($ssabado)->format('%h');}else{$hs=0;}

        //Primero, que no sobrepase las 8 hrs al dia
        if($hl+$lun>8){
            $mensaje="No fue posible procesar el horario ya que el lunes sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }
        if($hm+$mar>8){
            $mensaje="No fue posible procesar el horario ya que el martes sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }
        if($hmm+$mie>8){
            $mensaje="No fue posible procesar el horario ya que el miércoles sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }
        if($hj+$jue>8){
            $mensaje="No fue posible procesar el horario ya que el jueves sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }
        if($hv+$vie>8){
            $mensaje="No fue posible procesar el horario ya que el viernes sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }
        if($hs+$sab>8){
            $mensaje="No fue posible procesar el horario ya que el sábado sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }

        //Sólo por si acaso, las horas no pueden ser negativas
        if(($hl<0)||($hm<0)||($hmm<0)||($hj<0)||($hv<0)||($hs<0)){
            $mensaje="La hora de salida no puede ser mayor a la de entrada";
            return view('acad.no')->with(compact('mensaje'));
        }
        //Que no exista cruce
        $cant=DB::table('horario_administrativo')->where('periodo',$periodo)
            ->where('rfc',$rfc)->count();
        DB::table('horario_administrativo')->insert([
            'periodo'=>$periodo,
            'rfc'=>$rfc,
            'consecutivo_admvo'=>$cant+1,
            'descripcion_horario'=>$actividad,
            'fcaptura'=>Carbon::now()
        ]);
        if(!empty($elunes)){
            try{
                DB::table('horarios')->insert([
                    'periodo'=>$periodo,
                    'rfc'=>$rfc,
                    'tipo_horario'=>'A',
                    'dia_semana'=>2,
                    'hora_inicial'=>$elunes,
                    'hora_final'=>$slunes,
                    'materia'=>null,
                    'grupo'=>null,
                    'aula'=>null,
                    'actividad'=>null,
                    'consecutivo'=>null,
                    'vigencia_inicio'=>null,
                    'vigencia_fin'=>null,
                    'consecutivo_admvo'=>$cant+1,
                    'tipo_personal'=>null,
                    'created_at'=>Carbon::now()
                ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el lunes";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        if(!empty($emartes)){
            try{
                DB::table('horarios')->insert([
                    'periodo'=>$periodo,
                    'rfc'=>$rfc,
                    'tipo_horario'=>'A',
                    'dia_semana'=>3,
                    'hora_inicial'=>$emartes,
                    'hora_final'=>$smartes,
                    'materia'=>null,
                    'grupo'=>null,
                    'aula'=>null,
                    'actividad'=>null,
                    'consecutivo'=>null,
                    'vigencia_inicio'=>null,
                    'vigencia_fin'=>null,
                    'consecutivo_admvo'=>$cant+1,
                    'tipo_personal'=>null,
                    'created_at'=>Carbon::now()
                ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el martes";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        if(!empty($emiercoles)){
            try{
                DB::table('horarios')->insert([
                    'periodo'=>$periodo,
                    'rfc'=>$rfc,
                    'tipo_horario'=>'A',
                    'dia_semana'=>4,
                    'hora_inicial'=>$emiercoles,
                    'hora_final'=>$smiercoles,
                    'materia'=>null,
                    'grupo'=>null,
                    'aula'=>null,
                    'actividad'=>null,
                    'consecutivo'=>null,
                    'vigencia_inicio'=>null,
                    'vigencia_fin'=>null,
                    'consecutivo_admvo'=>$cant+1,
                    'tipo_personal'=>null,
                    'created_at'=>Carbon::now()
                ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el miércoles";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        if(!empty($ejueves)){
            try{
                DB::table('horarios')->insert([
                    'periodo'=>$periodo,
                    'rfc'=>$rfc,
                    'tipo_horario'=>'A',
                    'dia_semana'=>5,
                    'hora_inicial'=>$ejueves,
                    'hora_final'=>$sjueves,
                    'materia'=>null,
                    'grupo'=>null,
                    'aula'=>null,
                    'actividad'=>null,
                    'consecutivo'=>null,
                    'vigencia_inicio'=>null,
                    'vigencia_fin'=>null,
                    'consecutivo_admvo'=>$cant+1,
                    'tipo_personal'=>null,
                    'created_at'=>Carbon::now()
                ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el jueves";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        if(!empty($eviernes)){
            try{
                DB::table('horarios')->insert([
                    'periodo'=>$periodo,
                    'rfc'=>$rfc,
                    'tipo_horario'=>'A',
                    'dia_semana'=>6,
                    'hora_inicial'=>$eviernes,
                    'hora_final'=>$sviernes,
                    'materia'=>null,
                    'grupo'=>null,
                    'aula'=>null,
                    'actividad'=>null,
                    'consecutivo'=>null,
                    'vigencia_inicio'=>null,
                    'vigencia_fin'=>null,
                    'consecutivo_admvo'=>$cant+1,
                    'tipo_personal'=>null,
                    'created_at'=>Carbon::now()
                ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el viernes";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        if(!empty($esabado)){
            try{
                DB::table('horarios')->insert([
                    'periodo'=>$periodo,
                    'rfc'=>$rfc,
                    'tipo_horario'=>'A',
                    'dia_semana'=>7,
                    'hora_inicial'=>$esabado,
                    'hora_final'=>$ssabado,
                    'materia'=>null,
                    'grupo'=>null,
                    'aula'=>null,
                    'actividad'=>null,
                    'consecutivo'=>null,
                    'vigencia_inicio'=>null,
                    'vigencia_fin'=>null,
                    'consecutivo_admvo'=>$cant+1,
                    'tipo_personal'=>null,
                    'created_at'=>Carbon::now()
                ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el sábado";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        return view('acad.si');
    }
    public function modificaadmvo($periodo,$rfc,$consecutivo){
        $consecutivo=(int)$consecutivo;
        $puestos=DB::table('puestos')->get();
        $puesto=Db::table('horario_administrativo')->where('periodo',$periodo)
            ->where('rfc',$rfc)
            ->where('consecutivo_admvo',$consecutivo)
            ->select('descripcion_horario')->first();
        $info=DB::table('grupos')->where('periodo',$periodo)->where('rfc',$rfc)
            ->whereNull('paralelo_de')
            ->join('materias_carreras as mc','mc.materia','=','grupos.materia')
            ->join('materias','mc.materia','=','materias.materia')
            ->select('grupos.materia','grupo','nombre_abreviado_materia')
            ->distinct('grupos.materia')
            ->get();
        $nperiodo=DB::table('periodos_escolares')->where('periodo',$periodo)->first();
        return view('acad.mod_hadmvo')->with(compact('periodo','rfc','consecutivo','puestos','puesto','info','nperiodo'));
    }
    public function eliminaadmvo($periodo,$rfc,$consecutivo){
        DB::table('horario_administrativo')->where('periodo',$periodo)->where('rfc',$rfc)
            ->where('consecutivo_admvo',$consecutivo)->delete();
        DB::table('horarios')->where('periodo',$periodo)->where('rfc',$rfc)->where('tipo_horario','A')
            ->where('consecutivo_admvo',$consecutivo)->delete();
        return view('acad.si');
    }
    public function procesoadmvoupdate(Request $request){
        $data=request()->validate([
            'slunes'=>'required_with:elunes',
            'smartes'=>'required_with:emartes',
            'smiercoles'=>'required_with:emiercoles',
            'sjueves'=>'required_with:ejueves',
            'sviernes'=>'required_with:eviernes',
            'ssabado'=>'required_with:esabado',
        ],[
            'slunes.required_with'=>'Debe indicar la hora de salida para el lunes',
            'smartes.required_with'=>'Debe indicar la hora de salida para el martes',
            'smiercoles.required_with'=>'Debe indicar la hora de salida para el miércoles',
            'sjueves.required_with'=>'Debe indicar la hora de salida para el jueves',
            'sviernes.required_with'=>'Debe indicar la hora de salida para el viernes',
            'ssabado.required_with'=>'Debe indicar la hora de salida para el sabado',
        ]);
        $periodo=$request->get('periodo');
        $actividad=$request->get('puesto');
        $rfc=$request->get('rfc');
        $cant=$request->get('consecutivo');
        $lun=$request->get('hl');
        $mar=$request->get('hm');
        $mie=$request->get('hmm');
        $jue=$request->get('hj');
        $vie=$request->get('hv');
        $sab=$request->get('hs');
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

        if(!empty($elunes)){$hl=$elunes->diff($slunes)->format('%h');}else{$hl=0;}
        if(!empty($emartes)){$hm=$emartes->diff($smartes)->format('%h');}else{$hm=0;}
        if(!empty($emiercoles)){$hmm=$emiercoles->diff($smiercoles)->format('%h');}else{$hmm=0;}
        if(!empty($ejueves)){$hj=$ejueves->diff($sjueves)->format('%h');}else{$hj=0;}
        if(!empty($eviernes)){$hv=$eviernes->diff($sviernes)->format('%h');}else{$hv=0;}
        if(!empty($esabado)){$hs=$esabado->diff($ssabado)->format('%h');}else{$hs=0;}

        //Primero, que no sobrepase las 8 hrs al dia
        if($hl+$lun>8){
            $mensaje="No fue posible procesar el horario ya que el lunes sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }
        if($hm+$mar>8){
            $mensaje="No fue posible procesar el horario ya que el martes sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }
        if($hmm+$mie>8){
            $mensaje="No fue posible procesar el horario ya que el miércoles sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }
        if($hj+$jue>8){
            $mensaje="No fue posible procesar el horario ya que el jueves sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }
        if($hv+$vie>8){
            $mensaje="No fue posible procesar el horario ya que el viernes sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }
        if($hs+$sab>8){
            $mensaje="No fue posible procesar el horario ya que el sábado sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }
        //Sólo por si acaso, las horas no pueden ser negativas
        if(($hl<0)||($hm<0)||($hmm<0)||($hj<0)||($hv<0)||($hs<0)){
            $mensaje="La hora de salida no puede ser mayor a la de entrada";
            return view('acad.no')->with(compact('mensaje'));
        }
        DB::table('horario_administrativo')->where('periodo',$periodo)->where('rfc',$rfc)
            ->where('consecutivo_admvo',$cant)->update([
                'descripcion_horario'=>$actividad
            ]);
        //Que no exista cruce
        if(!empty($elunes)){
            try{
                DB::table('horarios')->where('periodo',$periodo)
                ->where('rfc',$rfc)->where('tipo_horario','A')->where('consecutivo_admvo',$cant)
                    ->where('dia_semana',2)->update([
                        'hora_inicial'=>$elunes,
                        'hora_final'=>$slunes,
                        'updated_at'=>Carbon::now()
                    ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el lunes";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        if(!empty($emartes)){
            try{
                DB::table('horarios')->where('periodo',$periodo)
                    ->where('rfc',$rfc)->where('tipo_horario','A')->where('consecutivo_admvo',$cant)
                    ->where('dia_semana',3)->update([
                        'hora_inicial'=>$emartes,
                        'hora_final'=>$smartes,
                        'updated_at'=>Carbon::now()
                    ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el martes";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        if(!empty($emiercoles)){
            try{
                DB::table('horarios')->where('periodo',$periodo)
                    ->where('rfc',$rfc)->where('tipo_horario','A')->where('consecutivo_admvo',$cant)
                    ->where('dia_semana',4)->update([
                        'hora_inicial'=>$emiercoles,
                        'hora_final'=>$smiercoles,
                        'updated_at'=>Carbon::now()
                    ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el miércoles";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        if(!empty($ejueves)){
            try{
                DB::table('horarios')->where('periodo',$periodo)
                    ->where('rfc',$rfc)->where('tipo_horario','A')->where('consecutivo_admvo',$cant)
                    ->where('dia_semana',5)->update([
                        'hora_inicial'=>$ejueves,
                        'hora_final'=>$sjueves,
                        'updated_at'=>Carbon::now()
                    ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el jueves";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        if(!empty($eviernes)){
            try{
                DB::table('horarios')->where('periodo',$periodo)
                    ->where('rfc',$rfc)->where('tipo_horario','A')->where('consecutivo_admvo',$cant)
                    ->where('dia_semana',6)->update([
                        'hora_inicial'=>$eviernes,
                        'hora_final'=>$sviernes,
                        'updated_at'=>Carbon::now()
                    ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el viernes";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        if(!empty($esabado)){
            try{
                DB::table('horarios')->where('periodo',$periodo)
                    ->where('rfc',$rfc)->where('tipo_horario','A')->where('consecutivo_admvo',$cant)
                    ->where('dia_semana',7)->update([
                        'hora_inicial'=>$esabado,
                        'hora_final'=>$ssabado,
                        'updated_at'=>Carbon::now()
                    ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el sábado";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        return view('acad.si');
    }
    public function procesaapoyoalta(Request $request){
        $data=request()->validate([
            'especificar'=>'required',
            'slunes'=>'required_with:elunes',
            'smartes'=>'required_with:emartes',
            'smiercoles'=>'required_with:emiercoles',
            'sjueves'=>'required_with:ejueves',
            'sviernes'=>'required_with:eviernes',
            'ssabado'=>'required_with:esabado',
        ],[
            'especificar.required'=>'Debe detallar la acción a realizar',
            'slunes.required_with'=>'Debe indicar la hora de salida para el lunes',
            'smartes.required_with'=>'Debe indicar la hora de salida para el martes',
            'smiercoles.required_with'=>'Debe indicar la hora de salida para el miércoles',
            'sjueves.required_with'=>'Debe indicar la hora de salida para el jueves',
            'sviernes.required_with'=>'Debe indicar la hora de salida para el viernes',
            'ssabado.required_with'=>'Debe indicar la hora de salida para el sabado',
        ]);
        $periodo=$request->get('periodo');
        $actividad=$request->get('apoyo');
        $especificar=$request->get('especificar');
        $rfc=$request->get('rfc');
        $lun=$request->get('hl');
        $mar=$request->get('hm');
        $mie=$request->get('hmm');
        $jue=$request->get('hj');
        $vie=$request->get('hv');
        $sab=$request->get('hs');
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

        if(!empty($elunes)){$hl=$elunes->diff($slunes)->format('%h');}else{$hl=0;}
        if(!empty($emartes)){$hm=$emartes->diff($smartes)->format('%h');}else{$hm=0;}
        if(!empty($emiercoles)){$hmm=$emiercoles->diff($smiercoles)->format('%h');}else{$hmm=0;}
        if(!empty($ejueves)){$hj=$ejueves->diff($sjueves)->format('%h');}else{$hj=0;}
        if(!empty($eviernes)){$hv=$eviernes->diff($sviernes)->format('%h');}else{$hv=0;}
        if(!empty($esabado)){$hs=$esabado->diff($ssabado)->format('%h');}else{$hs=0;}

        //Primero, que no sobrepase las 8 hrs al dia
        if($hl+$lun>8){
            $mensaje="No fue posible procesar el horario ya que el lunes sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }
        if($hm+$mar>8){
            $mensaje="No fue posible procesar el horario ya que el martes sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }
        if($hmm+$mie>8){
            $mensaje="No fue posible procesar el horario ya que el miércoles sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }
        if($hj+$jue>8){
            $mensaje="No fue posible procesar el horario ya que el jueves sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }
        if($hv+$vie>8){
            $mensaje="No fue posible procesar el horario ya que el viernes sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }
        if($hs+$sab>8){
            $mensaje="No fue posible procesar el horario ya que el sábado sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }

        //Sólo por si acaso, las horas no pueden ser negativas
        if(($hl<0)||($hm<0)||($hmm<0)||($hj<0)||($hv<0)||($hs<0)){
            $mensaje="La hora de salida no puede ser mayor a la de entrada";
            return view('acad.no')->with(compact('mensaje'));
        }
        //Que no exista cruce
        $cant=DB::table('apoyo_docencia')->where('periodo',$periodo)
            ->where('rfc',$rfc)->count();
        DB::table('apoyo_docencia')->insert([
            'periodo'=>$periodo,
            'rfc'=>$rfc,
            'actividad'=>$actividad,
            'consecutivo'=>$cant+1,
            'especifica_actividad'=>$especificar
        ]);
        if(!empty($elunes)){
            try{
                DB::table('horarios')->insert([
                    'periodo'=>$periodo,
                    'rfc'=>$rfc,
                    'tipo_horario'=>'Y',
                    'dia_semana'=>2,
                    'hora_inicial'=>$elunes,
                    'hora_final'=>$slunes,
                    'materia'=>null,
                    'grupo'=>null,
                    'aula'=>null,
                    'actividad'=>null,
                    'consecutivo'=>$cant+1,
                    'vigencia_inicio'=>null,
                    'vigencia_fin'=>null,
                    'consecutivo_admvo'=>null,
                    'tipo_personal'=>null,
                    'created_at'=>Carbon::now()
                ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el lunes";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        if(!empty($emartes)){
            try{
                DB::table('horarios')->insert([
                    'periodo'=>$periodo,
                    'rfc'=>$rfc,
                    'tipo_horario'=>'Y',
                    'dia_semana'=>3,
                    'hora_inicial'=>$emartes,
                    'hora_final'=>$smartes,
                    'materia'=>null,
                    'grupo'=>null,
                    'aula'=>null,
                    'actividad'=>null,
                    'consecutivo'=>$cant+1,
                    'vigencia_inicio'=>null,
                    'vigencia_fin'=>null,
                    'consecutivo_admvo'=>null,
                    'tipo_personal'=>null,
                    'created_at'=>Carbon::now()
                ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el martes";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        if(!empty($emiercoles)){
            try{
                DB::table('horarios')->insert([
                    'periodo'=>$periodo,
                    'rfc'=>$rfc,
                    'tipo_horario'=>'Y',
                    'dia_semana'=>4,
                    'hora_inicial'=>$emiercoles,
                    'hora_final'=>$smiercoles,
                    'materia'=>null,
                    'grupo'=>null,
                    'aula'=>null,
                    'actividad'=>null,
                    'consecutivo'=>$cant+1,
                    'vigencia_inicio'=>null,
                    'vigencia_fin'=>null,
                    'consecutivo_admvo'=>null,
                    'tipo_personal'=>null,
                    'created_at'=>Carbon::now()
                ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el miércoles";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        if(!empty($ejueves)){
            try{
                DB::table('horarios')->insert([
                    'periodo'=>$periodo,
                    'rfc'=>$rfc,
                    'tipo_horario'=>'Y',
                    'dia_semana'=>5,
                    'hora_inicial'=>$ejueves,
                    'hora_final'=>$sjueves,
                    'materia'=>null,
                    'grupo'=>null,
                    'aula'=>null,
                    'actividad'=>null,
                    'consecutivo'=>$cant+1,
                    'vigencia_inicio'=>null,
                    'vigencia_fin'=>null,
                    'consecutivo_admvo'=>null,
                    'tipo_personal'=>null,
                    'created_at'=>Carbon::now()
                ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el jueves";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        if(!empty($eviernes)){
            try{
                DB::table('horarios')->insert([
                    'periodo'=>$periodo,
                    'rfc'=>$rfc,
                    'tipo_horario'=>'Y',
                    'dia_semana'=>6,
                    'hora_inicial'=>$eviernes,
                    'hora_final'=>$sviernes,
                    'materia'=>null,
                    'grupo'=>null,
                    'aula'=>null,
                    'actividad'=>null,
                    'consecutivo'=>$cant+1,
                    'vigencia_inicio'=>null,
                    'vigencia_fin'=>null,
                    'consecutivo_admvo'=>null,
                    'tipo_personal'=>null,
                    'created_at'=>Carbon::now()
                ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el viernes";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        if(!empty($esabado)){
            try{
                DB::table('horarios')->insert([
                    'periodo'=>$periodo,
                    'rfc'=>$rfc,
                    'tipo_horario'=>'Y',
                    'dia_semana'=>7,
                    'hora_inicial'=>$esabado,
                    'hora_final'=>$ssabado,
                    'materia'=>null,
                    'grupo'=>null,
                    'aula'=>null,
                    'actividad'=>null,
                    'consecutivo'=>$cant+1,
                    'vigencia_inicio'=>null,
                    'vigencia_fin'=>null,
                    'consecutivo_admvo'=>null,
                    'tipo_personal'=>null,
                    'created_at'=>Carbon::now()
                ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el sábado";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        return view('acad.si');
    }
    public function modificaapoyo($periodo,$rfc,$consecutivo){
        $consecutivo=(int)$consecutivo;
        $puestos=DB::table('actividades_apoyo')->get();
        $puesto=Db::table('apoyo_docencia')->where('periodo',$periodo)
            ->where('rfc',$rfc)
            ->where('consecutivo',$consecutivo)
            ->select('especifica_actividad','actividad')->first();
        $info=DB::table('grupos')->where('periodo',$periodo)->where('rfc',$rfc)
            ->whereNull('paralelo_de')
            ->join('materias_carreras as mc','mc.materia','=','grupos.materia')
            ->join('materias','mc.materia','=','materias.materia')
            ->select('grupos.materia','grupo','nombre_abreviado_materia')
            ->distinct('grupos.materia')
            ->get();
        $nperiodo=DB::table('periodos_escolares')->where('periodo',$periodo)->first();
        return view('acad.mod_hapoyo')->with(compact('periodo','rfc','consecutivo','puestos','puesto','info','nperiodo'));
    }
    public function procesoapoyoupdate(Request $request){
        $data=request()->validate([
            'especificar'=>'required',
            'slunes'=>'required_with:elunes',
            'smartes'=>'required_with:emartes',
            'smiercoles'=>'required_with:emiercoles',
            'sjueves'=>'required_with:ejueves',
            'sviernes'=>'required_with:eviernes',
            'ssabado'=>'required_with:esabado',
        ],[
            'especificar.required'=>'Debe detallar la acción a realizar',
            'slunes.required_with'=>'Debe indicar la hora de salida para el lunes',
            'smartes.required_with'=>'Debe indicar la hora de salida para el martes',
            'smiercoles.required_with'=>'Debe indicar la hora de salida para el miércoles',
            'sjueves.required_with'=>'Debe indicar la hora de salida para el jueves',
            'sviernes.required_with'=>'Debe indicar la hora de salida para el viernes',
            'ssabado.required_with'=>'Debe indicar la hora de salida para el sabado',
        ]);
        $periodo=$request->get('periodo');
        $actividad=$request->get('puesto');
        $especificar=$request->get('especificar');
        $rfc=$request->get('rfc');
        $cant=$request->get('consecutivo');
        $lun=$request->get('hl');
        $mar=$request->get('hm');
        $mie=$request->get('hmm');
        $jue=$request->get('hj');
        $vie=$request->get('hv');
        $sab=$request->get('hs');
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

        if(!empty($elunes)){$hl=$elunes->diff($slunes)->format('%h');}else{$hl=0;}
        if(!empty($emartes)){$hm=$emartes->diff($smartes)->format('%h');}else{$hm=0;}
        if(!empty($emiercoles)){$hmm=$emiercoles->diff($smiercoles)->format('%h');}else{$hmm=0;}
        if(!empty($ejueves)){$hj=$ejueves->diff($sjueves)->format('%h');}else{$hj=0;}
        if(!empty($eviernes)){$hv=$eviernes->diff($sviernes)->format('%h');}else{$hv=0;}
        if(!empty($esabado)){$hs=$esabado->diff($ssabado)->format('%h');}else{$hs=0;}

        //Primero, que no sobrepase las 8 hrs al dia
        if($hl+$lun>8){
            $mensaje="No fue posible procesar el horario ya que el lunes sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }
        if($hm+$mar>8){
            $mensaje="No fue posible procesar el horario ya que el martes sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }
        if($hmm+$mie>8){
            $mensaje="No fue posible procesar el horario ya que el miércoles sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }
        if($hj+$jue>8){
            $mensaje="No fue posible procesar el horario ya que el jueves sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }
        if($hv+$vie>8){
            $mensaje="No fue posible procesar el horario ya que el viernes sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }
        if($hs+$sab>8){
            $mensaje="No fue posible procesar el horario ya que el sábado sobrepasa las 8 horas al día";
            return view('acad.no')->with(compact('mensaje'));
        }
        //Sólo por si acaso, las horas no pueden ser negativas
        if(($hl<0)||($hm<0)||($hmm<0)||($hj<0)||($hv<0)||($hs<0)){
            $mensaje="La hora de salida no puede ser mayor a la de entrada";
            return view('acad.no')->with(compact('mensaje'));
        }
        DB::table('apoyo_docencia')->where('periodo',$periodo)->where('rfc',$rfc)
            ->where('consecutivo',$cant)->update([
                'actividad'=>$actividad,
                'especifica_actividad'=>$especificar
            ]);

        //Que no exista cruce
        if(!empty($elunes)){
            try{
                DB::table('horarios')->where('periodo',$periodo)
                    ->where('rfc',$rfc)->where('tipo_horario','Y')->where('consecutivo',$cant)
                    ->where('dia_semana',2)->update([
                        'hora_inicial'=>$elunes,
                        'hora_final'=>$slunes,
                        'updated_at'=>Carbon::now()
                    ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el lunes";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        if(!empty($emartes)){
            try{
                DB::table('horarios')->where('periodo',$periodo)
                    ->where('rfc',$rfc)->where('tipo_horario','Y')->where('consecutivo',$cant)
                    ->where('dia_semana',3)->update([
                        'hora_inicial'=>$emartes,
                        'hora_final'=>$smartes,
                        'updated_at'=>Carbon::now()
                    ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el martes";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        if(!empty($emiercoles)){
            try{
                DB::table('horarios')->where('periodo',$periodo)
                    ->where('rfc',$rfc)->where('tipo_horario','Y')->where('consecutivo',$cant)
                    ->where('dia_semana',4)->update([
                        'hora_inicial'=>$emiercoles,
                        'hora_final'=>$smiercoles,
                        'updated_at'=>Carbon::now()
                    ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el miércoles";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        if(!empty($ejueves)){
            try{
                DB::table('horarios')->where('periodo',$periodo)
                    ->where('rfc',$rfc)->where('tipo_horario','Y')->where('consecutivo',$cant)
                    ->where('dia_semana',5)->update([
                        'hora_inicial'=>$ejueves,
                        'hora_final'=>$sjueves,
                        'updated_at'=>Carbon::now()
                    ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el jueves";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        if(!empty($eviernes)){
            try{
                DB::table('horarios')->where('periodo',$periodo)
                    ->where('rfc',$rfc)->where('tipo_horario','Y')->where('consecutivo',$cant)
                    ->where('dia_semana',6)->update([
                        'hora_inicial'=>$eviernes,
                        'hora_final'=>$sviernes,
                        'updated_at'=>Carbon::now()
                    ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el viernes";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        if(!empty($esabado)){
            try{
                DB::table('horarios')->where('periodo',$periodo)
                    ->where('rfc',$rfc)->where('tipo_horario','Y')->where('consecutivo',$cant)
                    ->where('dia_semana',7)->update([
                        'hora_inicial'=>$esabado,
                        'hora_final'=>$ssabado,
                        'updated_at'=>Carbon::now()
                    ]);
            }catch (QueryException $e){
                $mensaje="La persona se encuentra ocupada en ese horario el sábado";
                return view('acad.no')->with(compact('mensaje'));
            }
        }
        return view('acad.si');
    }
    public function eliminaapoyo($periodo,$rfc,$consecutivo){
        DB::table('apoyo_docencia')->where('periodo',$periodo)->where('rfc',$rfc)
            ->where('consecutivo',$consecutivo)->delete();
        DB::table('horarios')->where('periodo',$periodo)->where('rfc',$rfc)->where('tipo_horario','Y')
            ->where('consecutivo',$consecutivo)->delete();
        return view('acad.si');
    }
    public function altaobservacion(Request $request){
        $data=request()->validate([
            'obs'=>'required'
        ],[
            'obs.required'=>'Debe indicar la observación correspondiente para el horario'
        ]);
        $periodo=$request->get('periodo');
        $obs=$request->get('obs');
        $rfc=$request->get('rfc');
        DB::table('horario_observaciones')->insert([
           'periodo'=>$periodo,
           'rfc'=>$rfc,
           'observaciones'=>$obs,
            'depto'=>null,
            'cuando'=>Carbon::now()
        ]);
        return view('acad.si');
    }
    public function modificaobservaciones($periodo,$rfc){
        $obs=DB::table('horario_observaciones')->where('periodo',$periodo)->where('rfc',$rfc)
            ->select('observaciones')->first();
        $personal=DB::table('personal')->where('rfc',$rfc)->select('apellidos_empleado','nombre_empleado')->first();
        return view('acad.mod_obs')->with(compact('periodo','personal','obs','rfc'));
    }
    public function observacionesupdate(Request $request){
        $data=request()->validate([
            'obs'=>'required'
        ],[
            'obs.required'=>'Debe indicar la observación correspondiente para el horario'
        ]);
        $periodo=$request->get('periodo');
        $obs=$request->get('obs');
        $rfc=$request->get('rfc');
        DB::table('horario_observaciones')->where('periodo',$periodo)->where('rfc',$rfc)
            ->update([
                'observaciones'=>$obs,
                'depto'=>null,
                'cuando'=>Carbon::now()
            ]);
        return view('acad.si');
    }
    public function eliminaobservaciones($periodo,$rfc){
        DB::table('horario_observaciones')->where('periodo',$periodo)->where('rfc',$rfc)
            ->delete();
        return view('acad.si');
    }
    public function contrasenia(){
        return view('acad.contrasenia');
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
        return view('acad.inicio');
    }
    public function cambiarcontra(){
        $personal=DB::table('personal')->where('inactivo_rc','N')->where('nombramiento','D')
            ->where('status_empleado','2')
            ->orderBy('apellidos_empleado','asc')->orderBy('nombre_empleado','asc')->get();
        return view('acad.contrasenia_personal1')->with(compact('personal'));
    }
    public function cambiarcontra2(Request $request){
        $rfc=$request->get('rfc');
        $info=DB::table('personal')->where('rfc',$rfc)->first();
        return view('acad.contrasenia_personal2')->with(compact('info'));
    }
    public function cambiarcontra3(Request $request){
        $data=request()->validate([
            'contra'=>'required|required_with:ccontra|same:ccontra',
            'ccontra'=>'required'
        ],[
            'contra.required'=>'Debe escribir la nueva contraseña',
            'contra.required_with'=>'Debe confirmar la contraseña',
            'contra.same'=>'No concuerda con la verificacion',
            'ccontra.required'=>'Debe confirmar la nueva contraseña'
        ]);
        $rfc=$request->get('rfc');
        $info=DB::table('personal')->where('rfc',$rfc)->first();
        $correo=$info->correo_institucion;
        $ncontra=bcrypt($request->get('contra'));
        DB::table('users')->where('email',$correo)->update([
            'password'=>$ncontra,
            'updated_at'=>Carbon::now()
        ]);
        return view('acad.si');
    }
    public function actareset(){
        $periodo="20203";
        $personal=DB::table('grupos')->where('periodo',$periodo)
            ->join('personal','grupos.rfc','=','personal.rfc')
            ->orderBy('apellidos_empleado','ASC')
            ->orderBy('nombre_empleado','ASC')
            ->distinct()->get(['personal.apellidos_empleado','personal.nombre_empleado','grupos.rfc']);
        return view('acad.reset1')->with(compact('personal'));
    }
    public function actareset2(Request $request){
        $periodo="20203";
        $rfc=$request->get('rfc');
        $materias=DB::table('grupos')->where('periodo',$periodo)
            ->where('rfc',$rfc)->join('materias','grupos.materia','=','materias.materia')
            ->select('materias.nombre_abreviado_materia','grupos.grupo','grupos.materia')
            ->orderBy('nombre_abreviado_materia')->get();
        $doc=DB::table('personal')->where('rfc',$rfc)->first();
        return view('acad.reset2')->with(compact('materias','doc','periodo','rfc'));
    }
    public function actareset3(Request $request){
        $periodo=$request->get('periodo');
        $rfc=$request->get('rfc');
        $datos_materia=$request->get('materia');
        $datos=explode("_",$datos_materia);
        $materia=$datos[0]; $grupo=$datos[1];
        DB::table('seleccion_materias')->where('periodo',$periodo)
            ->where('materia',$materia)->where('grupo',$grupo)->update([
               'calificacion'=>null,
               'updated_at'=>Carbon::now()
            ]);
        return view('acad.si');
    }
}
