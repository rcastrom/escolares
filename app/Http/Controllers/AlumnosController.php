<?php

namespace App\Http\Controllers;

use App\Alumnos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PDF;

class AlumnosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(){
        $data=Auth::user()->email;
        $info=explode('@',$data);
        $control=substr($info[0],2,strlen($info[0])-2);
        session(['control'=>strtoupper($control)]);
        return view('alumnos.inicio');
    }
    public function periodo(){
        $periodo_actual=Db::Select('select periodo from pac_periodo_actual()');
        return $periodo_actual;
    }
    public function materias($control){
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
    public function ver_horario($control,$periodo){
        $data=DB::select("select * from pac_horario('$control','$periodo')");
        return $data;
    }
    public function ver_calif($control,$periodo){
        //Primero, busco los periodos que ha tenido
        $data=DB::select("select * from calificaciones('$periodo','$control')");
        return $data;
    }
    public function ver_kardex(){
        $ncontrol=session('control');
        $calificaciones = $this->kardex($ncontrol);
        $nperiodos = $this->nperiodo($ncontrol);
        $alumno = Alumnos::findOrfail($ncontrol);
        $ncarrera = Db::table('carreras')->select('nombre_carrera', 'creditos_totales')
            ->where('carrera', $alumno->carrera)
            ->where('reticula', $alumno->reticula)
            ->get();
        $estatus = Db::table('estatus_alumno')->where('estatus', $alumno->estatus_alumno)->get();
        return view('alumnos.kardex')
            ->with(compact('alumno', 'calificaciones', 'estatus', 'ncarrera', 'nperiodos', 'ncontrol'));
    }
    public function calificacion_periodo($control,$periodo){
        $data=DB::select("select * from pac_calificacion_semestre('$control','$periodo')");
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
    public function horario(){
        $ncontrol=session('control');
        $alumno=Alumnos::findOrfail($ncontrol);
        $periodo_actual=$this->periodo();
        $per=$periodo_actual[0]->periodo;
        if(DB::table('seleccion_materias')
            ->where('no_de_control',$ncontrol)
            ->where('periodo',$periodo_actual[0]->periodo)
            ->count()>0){
            $datos_horario=$this->ver_horario($ncontrol,$per);
            $nombre_periodo=DB::table('periodos_escolares')->where('periodo',$periodo_actual[0]->periodo)->get();
            return view('alumnos.horario')->with(compact('alumno','datos_horario','nombre_periodo','periodo_actual'));
        }else{
            $mensaje="NO CUENTA CON CARGA ACADÉMICA ASIGNADA";
            return view('alumnos.no')->with(compact('mensaje'));
        }
    }
    public function reticula($control){
        $data=DB::select("select * from pac_reticulaalumno('$control')");
        return $data;
    }
    public function verifica_especial($control,$periodo){
        $data=DB::select("select * from pac_verifica_especial('$control','$periodo')");
        return $data;
    }
    public function verifica_repite($control,$periodo){
        $data=DB::select("select * from pac_verifica_repite('$control','$periodo')");
        return $data;
    }
    public function materias_evaluar($periodo,$control){
        $data=DB::select("select * from evl_omitir_mat_alu('$periodo','$control')");
        return $data;
    }
    public function boleta(){
        $ncontrol=session('control');
        $periodos=DB::table('historia_alumno')
            ->select('historia_alumno.periodo','periodos_escolares.identificacion_corta')
            ->where('no_de_control',$ncontrol)
            ->distinct()
            ->join('periodos_escolares','historia_alumno.periodo','=','periodos_escolares.periodo')
            ->orderBy('periodo','DESC')
            ->get();
        return view('alumnos.preboleta')->with(compact('periodos'));
    }
    public function verboleta(Request $request){
        $ncontrol=session('control');
        $alumno=Alumnos::findOrfail($ncontrol);
        $periodo=$request->get('pbusqueda');
        $calificaciones=$this->ver_calif($ncontrol,$periodo);
        $nombre_periodo=DB::table('periodos_escolares')->where('periodo',$periodo)->get();
        return view('alumnos.boleta')
            ->with(compact('alumno','calificaciones','nombre_periodo','periodo'));
    }
    public function verreticula(){
        $ncontrol=session('control');
        $alumno=Alumnos::findOrfail($ncontrol);
        $historial=$this->reticula($ncontrol);
        $periodo_actual=$this->periodo();
        $per=$periodo_actual[0]->periodo;
        $carga=DB::table('seleccion_materias')->where('periodo',$per)
            ->join('alumnos','alumnos.no_de_control','=','seleccion_materias.no_de_control')
            ->where('alumnos.no_de_control',$ncontrol)
            ->join('materias','seleccion_materias.materia','=','materias.materia')
            ->join('materias_carreras as mc','seleccion_materias.materia','=','mc.materia')
            ->join('alumnos as al1','al1.carrera','=','mc.carrera')
            ->join('alumnos as al2','al2.reticula','=','mc.reticula')
            ->selectRaw('distinct(seleccion_materias.materia),grupo,nombre_abreviado_materia,creditos_materia,repeticion,global')
            ->get();
        return view('alumnos.reticula')->with(compact('alumno','historial','carga'));
    }
    public function vercalificaciones(){
        $ncontrol=session('control');
        $alumno=Alumnos::findOrfail($ncontrol);
        $periodo_actual=$this->periodo();
        $per=$periodo_actual[0]->periodo;
        $nperiodo=DB::table('periodos_escolares')->where('periodo',$per)->select('identificacion_corta')->first();
        $carga=$this->calificacion_periodo($ncontrol,$per);
        return view('alumnos.vercalificaciones')->with(compact('alumno','carga','nperiodo'));
    }
    public function reinscripcion(){
        $ncontrol=session('control');
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        //Primero, si es etapa para poder seleccionar materias
        $enfecha=DB::select('SELECT 1 AS si FROM periodos_escolares WHERE periodo = :periodo
        AND CURRENT_DATE BETWEEN inicio_sele_alumnos AND fin_sele_alumnos',['periodo'=>$periodo]);
        if(!empty($enfecha)) {
            //Segundo, localizar si puede reincribirse
            if(DB::table('avisos_reinscripcion')->where('periodo',$periodo)->where('no_de_control',$ncontrol)->count()>0){
                //Ahora, si tiene pago registrado
                if(DB::table('avisos_reinscripcion')->where('periodo',$periodo)
                        ->where('no_de_control',$ncontrol)->where('autoriza_escolar','S')->count()>0){
                    //Si está en su momento
                    $reinscribir=DB::select('SELECT 1 AS si FROM avisos_reinscripcion WHERE periodo = :periodo
        AND no_de_control = :control AND CURRENT_TIMESTAMP > fecha_hora_seleccion',['periodo'=>$periodo,'control'=>$ncontrol]);
                    if(!empty($reinscribir)){
                        $alumno=Alumnos::findOrfail($ncontrol);
                        $historial=$this->reticula($ncontrol);
                        $carga=DB::table('seleccion_materias')->where('periodo',$periodo)
                            ->join('alumnos','alumnos.no_de_control','=','seleccion_materias.no_de_control')
                            ->where('alumnos.no_de_control',$ncontrol)
                            ->join('materias','seleccion_materias.materia','=','materias.materia')
                            ->join('materias_carreras as mc','seleccion_materias.materia','=','mc.materia')
                            ->join('alumnos as al1','al1.carrera','=','mc.carrera')
                            ->join('alumnos as al2','al2.reticula','=','mc.reticula')
                            ->selectRaw('distinct(seleccion_materias.materia),grupo,nombre_abreviado_materia,creditos_materia,repeticion,global')
                            ->get();
                        return view('alumnos.reinscripcion')->with(compact('alumno','historial','carga'));
                    }else{
                        $cuando=DB::table('avisos_reinscripcion')->where('periodo',$periodo)->where('no_de_control',$ncontrol)
                            ->select('fecha_hora_seleccion')->first();
                        if(empty($cuando)){
                            $mensaje="NO estás en tiempo para seleccionar tus materias";
                        }else{
                            $mensaje="NO estás en tiempo para seleccionar tus materias, te corresponde a ".$cuando->fecha_hora_seleccion;
                        }
                        return view('alumnos.no')->with(compact('mensaje'));
                    }
                }else{
                    $mensaje="El pago aún no se encuentra registrado";
                    return view('alumnos.no')->with(compact('mensaje'));
                }
            }else{
                $mensaje="Requiere autorización de Servicios Escolares para continuar";
                return view('alumnos.no')->with(compact('mensaje'));
            }
        }else{
            $mensaje="El período de reinscripción o no ha iniciado o no ha terminado";
            return view('alumnos.no')->with(compact('mensaje'));
        }
    }
    public function seleccion_materia($materia,$tipocur){
        $ncontrol=session('control');
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        $alumno=Alumnos::findOrfail($ncontrol);
        //Primero, determinar si existe la materia en su plan de estudios
        if(Db::table('materias_carreras')->where('materia',$materia)
            ->join('alumnos as a1','a1.carrera','=','materias_carreras.carrera')
            ->join('alumnos as a2','a2.reticula','=','materias_carreras.reticula')
            ->where('a1.no_de_control',$ncontrol)->count()>0){
            //Ahora, ver si están ofertando la materia
            if(DB::table('grupos')->where('periodo',$periodo)->where('materia',$materia)->count()>0){
                $cminima=DB::table('alumnos')->where('no_de_control',$ncontrol)
                    ->join('carreras as c1','c1.carrera','=','alumnos.carrera')
                    ->join('carreras as c2','c2.reticula','=','alumnos.reticula')
                    ->select('c1.carga_minima')->first();
                $cmaxima=DB::table('avisos_reinscripcion')->where('periodo',$periodo)
                    ->where('no_de_control',$ncontrol)->select('creditos_autorizados')->first();
                if(DB::table('historia_alumno')->where('no_de_control',$ncontrol)->where('calificacion','=',0)
                    ->groupBy('materia')->selectRaw('count(materia) as veces')->get()->isEmpty()){
                    $bandera_espe=0;
                    $bandera_rep=0;
                }else{
                    $espe=DB::table('historia_alumno')->where('no_de_control',$ncontrol)->where('calificacion','=',0)
                        ->groupBy('materia')->selectRaw('count(materia) as veces')->get();
                    foreach ($espe as $especiales){
                        if($especiales->veces>1){
                            $bandera_espe=1;
                            $bandera_rep=1;
                        }else{
                            $bandera_espe=0;
                            $bandera_rep=1;
                        }
                    }
                }
                $bandera_espe=0;
                $bandera_rep=0;
                //Adeuda especial, se verifica que la esté seleccionando primeramente
                if($bandera_espe==1){
                    $esp_adeud=DB::table('historia_alumno')->select('materia')
                        ->where('no_de_control',$ncontrol)
                        ->whereIn('tipo_evaluacion',['RO','RP','R1','R2'])->where('calificacion','<',70)
                        ->whereNotIn('materia',DB::table('historia_alumno')->where('no_de_control',$ncontrol)
                            ->where('tipo_evaluacion','CE')->where('calificacion','>=',70)
                            ->select('materia'))->get()->toArray();
                    if(!empty($esp_adeud)){
                        if(array_search($materia,array_column($esp_adeud,'materia'))){
                            $estatus="S"; $especial="S";
                        }else{
                            $estatus="N"; $especial="S";
                        }
                    }else{
                        $estatus="N"; $especial="N";
                    }
                    //La materia que seleccionó está en especial y no la está seleccionando
                    if($estatus=="N"){
                        $info_adicional=$this->verifica_especial($ncontrol,$periodo);
                        if($info_adicional[0]->adeudo>=2){
                            $mensaje="Tienes adeudo de 2 o mas especiales. No procede selección de esta materia";
                            return view('alumnos.no')->with(compact('mensaje'));
                        }elseif($info_adicional[0]->adeudo==1){
                            if($info_adicional[0]->pendientes>0){
                                $mensaje="Adeudas 1 especial. Debes seleccionar la materia correspondiente";
                                return view('alumnos.no')->with(compact('mensaje'));
                            }
                        }
                    }
                }else{
                    $especial="N";
                }
                //Se verifica si la materia es reprobada
                if($bandera_rep){
                    $rep_adeud=DB::table('historia_alumno')->select('materia')
                        ->where('no_de_control',$ncontrol)
                        ->whereIn('tipo_evaluacion',['OC','OO','1','2','01','02'])
                        ->where('calificacion','<',70)
                        ->whereNotIn('materia',DB::table('historia_alumno')->where('no_de_control',$ncontrol)
                            ->whereIn('tipo_evaluacion',['RO','RC','R1','R2'])
                            ->where('calificacion','>=',70)
                            ->select('materia'))->get()->toArray();
                    if(!empty($rep_adeud)){
                        if(array_search($materia,array_column($rep_adeud,'materia'))){
                            $sel="S";
                        }else{
                            $sel="N";
                        }
                    }else{
                        $sel="S";
                    }
                    if($sel=="N"){
                        $repite=$this->verifica_repite($ncontrol,$periodo);
                        if($repite[0]->pendientes > 0){
                            if($repite[0]->ofertados > $repite[0]->seleccionadas){
                                $mensaje="Adeudas materias en repetición. No procede selección de materia";
                                return view('alumnos.no')->with(compact('mensaje'));
                            }else{
                                $mensaje="Adeudas materias en repetición. No procede selección de materia";
                                return view('alumnos.no')->with(compact('mensaje','alumno'));
                            }
                        }
                    }
                }
                //
                //Se indica el estatus de la materia
                if($tipocur == 'CR'){
                    $repeticion = 'S';
                }elseif($tipocur == 'AE'){
                    $repeticion = "E";
                }else {
                    $repeticion = 'N';
                }
                $nmateria=DB::table('materias')->select('nombre_abreviado_materia')->where('materia',$materia)->first();
            /////Mostrar grupos
                $info_grupos=DB::select("select * from pac_gruposmateria('$periodo','$ncontrol','$materia')");
                return view('alumnos.seleccion_materias')->with(compact('info_grupos','materia','alumno','nmateria','periodo','repeticion'));
                ///

            }else{
                $mensaje="La materia no se está ofertando para éste semestre";
                return view('alumnos.no')->with(compact('mensaje'));
            }
        }else{
            $mensaje="La materia no concuerda con tu plan de estudios";
            return view('alumnos.no')->with(compact('mensaje'));
        }
    }
    public function reinscribir(Request $request){
        $ncontrol=session('control');
        $mat=$request->get('materia');
        $periodo=$request->get('periodo');
        $repeticion=$request->get('repeticion');
        $data=explode("_",$mat);
        $materia=$data[0]; $grupo=$data[1];
        $globales="op_".$materia."_".$grupo;
        $global=$request->get($globales);
        $bandera=0;
        for($i=2;$i<=7;$i++){
            if(DB::table('horarios')->where('periodo',$periodo)->where('materia',$materia)
            ->where('grupo',$grupo)->where('dia_semana',$i)->get()->isNotEmpty()){
                $horas=DB::table('horarios')->where('periodo',$periodo)->where('materia',$materia)
                    ->where('grupo',$grupo)->where('dia_semana',$i)->select('hora_inicial','hora_final')->first();
                $hinicial=$horas->hora_inicial;
                $hfinal=$horas->hora_final;
                $cantidad=DB::select('select 1 as si from seleccion_materias SM, horarios H where SM.periodo = H.periodo
                and SM.materia = H.materia
                and SM.grupo = H.grupo
                and SM.periodo = :periodo
                and SM.no_de_control = :no_de_control
                and  H.dia_semana = :dia
                and  ( H.hora_inicial = :hora_inicial  or
                    ( (hora_inicial < :hora_inicial) and (:hora_inicial < hora_final) )  or
                    ( (hora_inicial < :hora_final) and (:hora_final < hora_final) )  or
                    ( (:hora_inicial < hora_inicial) and (hora_inicial < :hora_final)) or
                    ( (hora_inicial > :hora_inicial) and (hora_final < :hora_final))
                )',['periodo'=>$periodo,'no_de_control'=>$ncontrol,'dia'=>$i,'hora_inicial'=>$hinicial,'hora_final'=>$hfinal]);
                //$cantidad=DB::select("select cruce from cruce_materia('$periodo','$materia','$grupo','$ncontrol','$i')");
                if(!empty($cantidad)){
                    $bandera+=1;
                }
            }
        }
        if($bandera>0){
            $mensaje="No fue posible realizar el movimiento porque existe empalme con otro horario ya seleccionado";
            return view('alumnos.no')->with(compact('mensaje'));
        }else{
            if(DB::table('seleccion_materias')->where('periodo',$periodo)->where('materia',$materia)
                    ->where('no_de_control',$ncontrol)->count()>0){
                $mensaje="La materia ya está seleccionada por lo que no es posible volver a seleccionarla";
                return view('alumnos.no')->with(compact('mensaje'));
            }else{
                $inscritos=DB::table('grupos')->where('periodo',$periodo)->where('materia',$materia)
                    ->where('grupo',$grupo)->count();
                $cap=DB::table('grupos')->where('periodo',$periodo)->where('materia',$materia)
                    ->where('grupo',$grupo)->select('capacidad_grupo')->first();
                $capacidad=$cap->capacidad_grupo-1;
                DB::table('seleccion_materias')->insert([
                    'periodo'=>$periodo,
                    'no_de_control'=>$ncontrol,
                    'materia'=>$materia,
                    'grupo'=>$grupo,
                    'calificacion'=>null,
                    'tipo_evaluacion'=>null,
                    'repeticion'=>$repeticion,
                    'nopresento'=>'N',
                    'status_seleccion'=>'E',
                    'fecha_hora_seleccion'=>Carbon::now(),
                    'global'=>$global,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>null
                ]);
                DB::table('grupos')->where('periodo',$periodo)->where('materia',$materia)
                    ->where('grupo',$grupo)->update([
                        'alumnos_inscritos'=>$inscritos+1,
                        'capacidad_grupo'=>$capacidad
                    ]);
                return redirect('/estudiante/reinscripcion');
            }
        }
    }
    public function bajam(){
        $ncontrol=session('control');
        $alumno=Alumnos::findOrfail($ncontrol);
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        //Primero, si es etapa para poder seleccionar materias
        $enfecha=DB::select('SELECT 1 AS si FROM periodos_escolares WHERE periodo = :periodo
        AND CURRENT_DATE BETWEEN inicio_sele_alumnos AND fin_sele_alumnos',['periodo'=>$periodo]);
        if(!empty($enfecha)) {
            if(DB::table('seleccion_materias')
                    ->where('no_de_control',$ncontrol)
                    ->where('periodo',$periodo)
                    ->count()>0){
            $datos_horario=$this->ver_horario($ncontrol,$periodo);
            $nombre_periodo=DB::table('periodos_escolares')->where('periodo',$periodo_actual[0]->periodo)->get();
            return view('alumnos.horario_b')->with(compact('alumno','datos_horario','nombre_periodo','periodo_actual'));
        }else{
                $mensaje="NO CUENTA CON CARGA ACADÉMICA ASIGNADA";
                return view('alumnos.no')->with(compact('mensaje'));
            }
        }else{
            $mensaje="El proceso de reinscripción o no ha empezado o ha finalizado";
            return view('alumnos.no')->with(compact('mensaje'));
        }
    }
    public function baja_materia(Request $request){
        $ncontrol=session('control');
        $alumno=Alumnos::findOrfail($ncontrol);
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        $mat=$request->get('materia');
        $data=explode('_',$mat);
        $materia=$data[0]; $grupo=$data[1];
        DB::table('seleccion_materias')->where('periodo',$periodo)
            ->where('no_de_control',$ncontrol)->where('materia',$materia)
            ->where('grupo',$grupo)->delete();
        $inscritos=DB::table('grupos')->where('periodo',$periodo)->where('materia',$materia)
            ->where('grupo',$grupo)->count();
        $cap=DB::table('grupos')->where('periodo',$periodo)->where('materia',$materia)
            ->where('grupo',$grupo)->select('capacidad_grupo')->first();
        $capacidad=$cap->capacidad_grupo+1;

        DB::table('grupos')->where('periodo',$periodo)->where('materia',$materia)
            ->where('grupo',$grupo)->update([
                'alumnos_inscritos'=>$inscritos-1,
                'capacidad_grupo'=>$capacidad
            ]);
        return redirect('/estudiante');
    }
    public function evaluacion(){
        $ncontrol=session('control');
        $alumno=Alumnos::findOrfail($ncontrol);
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        $nombre_periodo=DB::table('periodos_escolares')->where('periodo',$periodo)->first();
        if(DB::table('seleccion_materias')
                ->where('no_de_control',$ncontrol)
                ->where('periodo',$periodo_actual[0]->periodo)
                ->count()>0){
            $materias=$this->materias_evaluar($periodo,$ncontrol);
            if(empty($materias)){
                $mensaje="Ya finalizaste la evaluación docente del período ".$nombre_periodo->identificacion_corta;
                return view('alumnos.no')->with(compact('mensaje'));
            }else{
                $carga=array();
                $i=1;
                foreach ($materias as $key=>$values){
                    $mat=$values->materia;
                    $nmat=DB::table('materias')->where('materia',$mat)->first();
                    $nombre_mat=$nmat->nombre_abreviado_materia;
                    $gpo=$values->grupo;
                    $carga[$i]=$mat."_".$gpo."_".$nombre_mat;
                    $i++;
                }
            }
            return view('alumnos.preencuesta')->with(compact('nombre_periodo','carga'));
        }else{
            $mensaje="NO CUENTA CON CARGA ACADÉMICA ASIGNADA";
            return view('alumnos.no')->with(compact('mensaje'));
        }
    }
    public function evaluar(Request $request){
        $materia=$request->get('materia');
        $data=explode("_",$materia);
        $mat=$data[0]; $gpo=$data[1];
        $ncontrol=session('control');
        $alumno=Alumnos::findOrfail($ncontrol);
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        $nperiodo=DB::table('periodos_escolares')->where('periodo',$periodo)->first();
        $encuesta='A';
        $preguntas=DB::table('preguntas')->where('encuesta',$encuesta)
            ->where('consecutivo','=',2)->get();
        $nmat=DB::table('materias')->where('materia',$mat)->first();
        $doc=DB::table('grupos')->where('periodo',$periodo)->where('materia',$mat)->where('grupo',$gpo)
            ->first();
        if(is_null($doc->rfc)){
            $rfc="NULL"; $ndoc="POR ASIGNAR";
        }else{
            $rfc=$doc->rfc;
            $nombre_maestro=DB::table('personal')->where('rfc',$doc->rfc)->first();
            $ndoc=trim($nombre_maestro->nombre_empleado)." ".trim($nombre_maestro->apellidos_empleado);
        }
        return view('alumnos.encuesta')->with(compact('mat','gpo','nmat','preguntas','rfc','ndoc','nperiodo'));
    }
    public function evaluaciondoc(Request $request){
        $materia=$request->get('materia');
        $gpo=$request->get('gpo');
        $rfc=$request->get('rfc');
        $ncontrol=session('control');
        $alumno=Alumnos::findOrfail($ncontrol);
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        $nperiodo=DB::table('periodos_escolares')->where('periodo',$periodo)->first();
        $respuestas="";
        foreach ($request->all() as $key=>$value){
            if(($key!="materia")&&($key!="gpo")&&($key!="rfc")&&($key!="_token")){
                $respuestas.=$value;
            }
        }
        $respuesta=trim($respuestas);
        if(is_null($rfc)){
            $cve="NULL";
        }else{
            $doc=DB::table('personal')->where('rfc',$rfc)->first();
            $cve=$doc->clave_area;
        }
        DB::table('evaluacion_alumnos')->insert([
            'periodo'=>$periodo,
            'no_de_control'=>$ncontrol,
            'materia'=>$materia,
            'grupo'=>$gpo,
            'rfc'=>$rfc,
            'clave_area'=>$cve,
            'encuesta'=>'A',
            'respuestas'=>$respuesta,
            'resp_abierta'=>'',
            'usuario'=>$ncontrol,
            'consecutivo'=>'2',
            'created_at'=>Carbon::now(),
            'updated_at'=>null
        ]);
        return redirect('/estudiante/eval');
    }
    public function accionkardex(Request $request){
        $ncontrol=session('control');
        $accion = $request->accion;
        $alumno = Alumnos::findOrfail($ncontrol);
        if ($accion == 1) {
            $calificaciones = $this->kardex($ncontrol);
            $carrera = DB::table('carreras')->where('carrera', $alumno->carrera)
                ->where('reticula', $alumno->reticula)->first();
            $nperiodos = $this->nperiodo($ncontrol);
            $data = [
                'alumno' => $alumno,
                'control' => $ncontrol,
                'carrera' => $carrera,
                'nperiodos' => $nperiodos,
                'calificaciones' => $calificaciones
            ];
            $pdf = PDF::loadView('alumnos.pdf_kardex', $data);
            return $pdf->download('kardex.pdf');
        }
    }
}
