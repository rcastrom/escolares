<?php

namespace App\Http\Controllers;

use App\Alumnos;
use App\SeleccionMaterias;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Personal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ListasExport;

class PersonalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(){
        $data=Auth::user()->email;
        $docente=Personal::select('rfc')->where('correo_institucion',$data)->get();
        session(['docente'=>$docente[0]->rfc]);
        return view('personal.inicio');
    }
    public function periodo(){
        $periodo_actual=Db::Select('select periodo from pac_periodo_actual()');
        return $periodo_actual;
    }
    public function residencias($periodo,$rfc){
        //Primero, busco los periodos que ha tenido
        $data=DB::select("select * from pac_cresidencias('$periodo','$rfc')");
        return $data;
    }
    public function inforesidencias($periodo,$rfc){
        //Primero, busco los periodos que ha tenido
        $data=DB::select("select * from pac_dataresidencias('$periodo','$rfc')");
        return $data;
    }
    public function historial($ncontrol){
        $data=DB::select("select * from pac_historia_escolar_alumno('$ncontrol')");
        return $data;
    }
    public function encurso(){
        $periodo_actual=$this->periodo();
        //$periodo=$periodo_actual[0]->periodo;
        $periodo="20203";
        $doc=session('docente');
        if(DB::table('grupos')->where('periodo',$periodo)->where('rfc',$doc)->count()>0){
            $materias=DB::table('grupos')
                ->where('periodo',$periodo)
                ->where('rfc',$doc)
                ->join('materias','grupos.materia','=','materias.materia')
                ->get();
            $nperiodo=DB::table('periodos_escolares')->where('periodo',$periodo)->get();
            return view('personal.prelistas')->with(compact('materias','nperiodo'));
        }else{
            $mensaje="No cuenta con grupos en el período actual";
            return view('personal.no')->with(compact('mensaje'));
        }
    }
    public function lista($materia,$grupo){
        $periodo_actual=$this->periodo();
        //$periodo=$periodo_actual[0]->periodo;
        $periodo="20203";
        $doc=session('docente');
        if(DB::table('seleccion_materias')->where('periodo',$periodo)
                ->where('materia',$materia)
                ->where('grupo',$grupo)
                ->count()>0){
            $inscritos=DB::table('seleccion_materias')->where('periodo',$periodo)
                ->where('materia',$materia)
                ->where('grupo',$grupo)
                ->join('alumnos','seleccion_materias.no_de_control','=','alumnos.no_de_control')
                ->orderBy('apellido_paterno','asc')
                ->orderBy('apellido_materno','asc')
                ->orderBy('nombre_alumno','asc')
                ->get();
            $nombre_mat=DB::table('materias')->where('materia',$materia)->get();
            $ndocente=Personal::where('correo_institucion',Auth::user()->email)
            ->select('apellidos_empleado','nombre_empleado')->get();
            $nperiodo=DB::table('periodos_escolares')->where('periodo',$periodo)->get();
            $data=[
                'alumnos'=>$inscritos,
                'docente'=>$ndocente,
                'nombre_periodo'=>$nperiodo,
                'nmateria'=>$nombre_mat
            ];
            $pdf = PDF::loadView('personal.pdf_lista', $data)
                ->setPaper('Letter');
            return $pdf->download('lista.pdf');
        }else{
            $mensaje="No cuenta con alumnos inscritos en la materia";
            return view('personal.no')->with(compact('mensaje'));
        }
    }
    public function acta($materia,$grupo){
        $periodo_actual=$this->periodo();
        //$periodo=$periodo_actual[0]->periodo;
        $periodo="20203";
        $doc=session('docente');
        if(DB::table('seleccion_materias')->where('periodo',$periodo)
                ->where('materia',$materia)
                ->where('grupo',$grupo)
                ->count()>0){
            if(DB::table('seleccion_materias')->where('periodo',$periodo)
                    ->where('materia',$materia)
                    ->where('grupo',$grupo)
                    ->whereNotNull('calificacion')
                    ->count()>0){
                $inscritos=DB::table('seleccion_materias')->where('periodo',$periodo)
                    ->where('materia',$materia)
                    ->where('grupo',$grupo)
                    ->join('alumnos','seleccion_materias.no_de_control','=','alumnos.no_de_control')
                    ->join('tipos_evaluacion', function ($join) {
                        $join->on('alumnos.plan_de_estudios', '=', 'tipos_evaluacion.plan_de_estudios')
                            ->on('tipos_evaluacion.tipo_evaluacion', '=', 'seleccion_materias.tipo_evaluacion');
                    })
                    ->orderBy('apellido_paterno','asc')
                    ->orderBy('apellido_materno','asc')
                    ->orderBy('nombre_alumno','asc')
                    ->get();
                $datos=DB::table('grupos')->where('periodo',$periodo)
                    ->where('materia',$materia)
                    ->where('grupo',$grupo)
                    ->get();
                $nombre_mat=DB::table('materias')->where('materia',$materia)->get();
                $ndocente=Personal::where('correo_institucion',Auth::user()->email)
                    ->select('apellidos_empleado','nombre_empleado')->get();
                $nperiodo=DB::table('periodos_escolares')->where('periodo',$periodo)->get();
                $data=[
                    'alumnos'=>$inscritos,
                    'docente'=>$ndocente,
                    'nombre_periodo'=>$nperiodo,
                    'datos'=>$datos,
                    'nmateria'=>$nombre_mat,
                    'materia'=>$materia,
                    'grupo'=>$grupo
                ];
                $pdf = PDF::loadView('personal.pdf_acta', $data)
                    ->setPaper('Letter');
                return $pdf->download('acta.pdf');
            }else{
                $mensaje="Aún no cuenta con calificaciones registradas";
                return view('personal.no')->with(compact('mensaje'));
            }
        }else{
            $mensaje="No cuenta con alumnos inscritos en la materia";
            return view('personal.no')->with(compact('mensaje'));
        }
    }
    public function excel($materia,$grupo){
        $periodo_actual=$this->periodo();
        //$periodo=$periodo_actual[0]->periodo;
        $periodo="20203";
        if(DB::table('seleccion_materias')->where('periodo',$periodo)
                ->where('materia',$materia)
                ->where('grupo',$grupo)
                ->count()>0){
            $inscritos=DB::table('seleccion_materias')
                ->where('periodo',$periodo)
                ->where('materia',$materia)
                ->where('grupo',$grupo)
                ->join('alumnos','seleccion_materias.no_de_control','=','alumnos.no_de_control')
                ->select('seleccion_materias.no_de_control','apellido_paterno','apellido_materno','nombre_alumno')
                ->orderBy('apellido_paterno','asc')
                ->orderBy('apellido_materno','asc')
                ->orderBy('nombre_alumno','asc')
                ->get();
            return Excel::download(new ListasExport($inscritos),'lista.xlsx');
        }else{
            $mensaje="No cuenta con alumnos inscritos en la materia";
            return view('personal.no')->with(compact('mensaje'));
        }
    }
    public function evaluar($materia,$grupo){
        $periodo_actual=$this->periodo();
        //$periodo=$periodo_actual[0]->periodo;
        $periodo="20203";
        $calificar=DB::select('SELECT 1 AS si FROM periodos_escolares WHERE periodo = :periodo
        AND CURRENT_DATE BETWEEN inicio_cal_docentes AND fin_cal_docentes',['periodo'=>$periodo]);
        if(!empty($calificar)) {
            if (DB::table('seleccion_materias')->where('periodo', $periodo)
                    ->where('materia', $materia)->where('grupo', $grupo)
                    ->whereNotNull('calificacion')->count() > 0){
                $mensaje="La materia ha sido evaluada";
                return view('personal.no')->with(compact('mensaje'));
            }else{
                $inscritos = DB::table('seleccion_materias')
                    ->where('periodo', $periodo)
                    ->where('materia', $materia)
                    ->where('grupo', $grupo)
                    ->join('alumnos', 'seleccion_materias.no_de_control', '=', 'alumnos.no_de_control')
                    ->select('seleccion_materias.no_de_control', 'apellido_paterno', 'apellido_materno', 'nombre_alumno')
                    ->orderBy('apellido_paterno', 'asc')
                    ->orderBy('apellido_materno', 'asc')
                    ->orderBy('nombre_alumno', 'asc')
                    ->get();
                $nombre_mat = DB::table('materias')->where('materia', $materia)->get();
                $ndocente = Personal::where('correo_institucion', Auth::user()->email)
                    ->select('apellidos_empleado', 'nombre_empleado')->get();
                $nperiodo = DB::table('periodos_escolares')->where('periodo', $periodo)->get();
                return view('personal.evaluar')
                    ->with(compact('inscritos', 'nombre_mat', 'ndocente', 'ndocente', 'nperiodo', 'materia', 'grupo'));
            }
        }else{
            $fechas=DB::table('periodos_escolares')->where('periodo',$periodo)->get();
            $inicio=$fechas[0]->inicio_cal_docentes;
            $fin=$fechas[0]->fin_cal_docentes;
            $mensaje="No se encuentra en período de captura de calificaciones, es del ".$inicio." al ".$fin;
            return view('personal.no')->with(compact('mensaje'));
        }
    }
    public function calificar(Request $request){
        $materia=$request->get('materia');
        $grupo=$request->get('grupo');
        $periodo_actual=$this->periodo();
        //$periodo=$periodo_actual[0]->periodo;
        $periodo="20203";
        $inscritos = DB::table('seleccion_materias')
            ->where('periodo', $periodo)
            ->where('materia', $materia)
            ->where('grupo', $grupo)
            ->select('no_de_control','repeticion')
            ->get();
        foreach ($inscritos as $alumnos) {
            $control = $alumnos->no_de_control;
            $rep=$alumnos->repeticion;
            $plan=Alumnos::select('plan_de_estudios')->where('no_de_control',$control)->get();
            $obtener=$materia."_".$control; $op="op_".$control;
            $cal=$request->get($obtener);
            $oport=$request->get($op);
            $oportunidad=$plan[0]->plan_de_estudios==3?
                ($rep=="S"?($oport==1?"RO":"RP"):($oport==1?"OO":"OC")):
                ($rep=="S"?($oport==1?"R1":"R2"):($oport==1?"1":"2"));
            $calificar=DB::table('seleccion_materias')
                ->where('periodo', $periodo)
                ->where('materia', $materia)
                ->where('grupo', $grupo)
                ->where('no_de_control',$control)
                ->update([
                    'calificacion'=>$cal,
                    'tipo_evaluacion'=>$oportunidad,
                    'updated_at'=>Carbon::now()
                ]);
        }
        return view('personal.gracias');
    }
    public function contrasenia(){
        return view('personal.contrasenia');
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
        return view('personal.inicio');
    }
    public function residencias1(){
        $periodo_actual=$this->periodo();
        //$periodo=$periodo_actual[0]->periodo;
        $periodo="20203";
        $periodos=DB::table('periodos_escolares')
            ->whereNotIn('periodo',array('99990','99999'))
            ->orderBy('periodo','desc')
            ->get();
        return view('personal.residencias1')->with(compact('periodo','periodos'));
    }
    public function residencias2(Request $request){
        $per_residencias=$request->get('per_res');
        $doc=session('docente');
        $cant=$this->residencias($per_residencias,$doc);
        if($cant[0]->cantidad==0){
            $mensaje="No cuenta con residencias asignadas en el período señalado";
            return view('personal.no')->with(compact('mensaje'));
        }else{
            //Esta sección debe ajustarse posteriormente
            $quienes=$this->inforesidencias($per_residencias,$doc);
            return view('personal.residencias2')->with(compact('per_residencias','quienes'));
        }
    }
    public function residenciaevaluar($per,$mat,$gpo,$ncontrol){
        $periodo=base64_decode($per);
        $materia=base64_decode($mat);
        $grupo=base64_decode($gpo);
        $control=base64_decode($ncontrol);
        $alumno=Alumnos::findOrfail($control);
        return view('personal.residencias3')->with(compact('periodo','materia','grupo','alumno','control'));
    }
    public function residenciascalifica(Request $request){
        $data=request()->validate([
            'calificacion'=>['required','not_regex:/^\b([1-9]|[1-6][0-9])\b/'],

        ],[
            'calificacion.required'=>'Debe escribir la calificacion',
            'calificacion.not_regex'=>'La calificación o es 0 o debe estar comprendida entre el 70 al 100'
        ]);
        $periodo=$request->get('periodo');
        $grupo=$request->get('grupo');
        $control=$request->get('control');
        $calificacion=$request->get('calificacion');
        $residencia=DB::table('seleccion_materias')->where('periodo',$periodo)
            ->where('no_de_control',$control)->where('grupo',$grupo)->first();
        $materia=$residencia->materia;
        $alumno=Alumnos::findOrfail($control);
        $plan=$alumno->plan_de_estudios;
        $veces=DB::table('historia_alumno')->where('materia',$materia)
            ->where('no_de_control',$control)->count();
        if($veces==1){ //Esta repitiendo
            switch($plan){
                case 1: $te="N1"; break;
                case 2: $te="R2"; break;
                case 3: $te="RO"; break;
                case 4: $te="R1"; break;
            }
        }else{
            switch($plan){
                case 1: $te="O1"; break;
                case 2: $te="O1"; break;
                case 3: $te="OO"; break;
                case 4: $te="1"; break;
            }
        }
        DB::table('seleccion_materias')
            ->where('periodo',$periodo)->where('materia','like',$materia)
            ->where('grupo',$grupo)->where('no_de_control',$control)
            ->update([
                'calificacion'=>$calificacion,
                'tipo_evaluacion'=>$te
            ]);
        $cantidad=DB::table('historia_alumno')->where('materia',$materia)
            ->where('no_de_control',$control)->where('grupo',$grupo)
            ->where('periodo',$periodo)
            ->count();
        $doc=session('docente');
        if($cantidad==1){
            if($calificacion!=0){
                DB::table('historia_alumno')->where('materia',$materia)
                    ->where('no_de_control',$control)->where('grupo',$grupo)
                    ->where('periodo',$periodo)->update([
                       'calificacion'=>$calificacion,
                       'tipo_evaluacion'=>$te,
                        'usuario'=>$doc,
                        'fecha_actualizacion'=>Carbon::now(),
                        'periodo_acredita_materia'=>$periodo
                    ]);
            }else{
                DB::table('historia_alumno')->where('materia',$materia)
                    ->where('no_de_control',$control)->where('grupo',$grupo)
                    ->where('periodo',$periodo)->update([
                        'calificacion'=>$calificacion,
                        'tipo_evaluacion'=>$te,
                        'usuario'=>$doc,
                        'fecha_actualizacion'=>Carbon::now()
                    ]);
            }
        }else{
            if($calificacion!=0){
                DB::table('historia_alumno')->insert([
                   'periodo'=>$periodo,
                   'no_de_control'=>$control,
                   'materia'=>$materia,
                   'grupo'=>$grupo,
                    'calificacion'=>$calificacion,
                    'tipo_evaluacion'=>$te,
                    'fecha_calificacion'=>Carbon::now(),
                    'plan_de_estudios'=>$plan,
                    'estatus_materia'=>'A',
                    'nopresento'=>null,
                    'usuario'=>$doc,
                    'fecha_actualizacion'=>null,
                    'periodo_acredita_materia'=>$periodo,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>null
                ]);
            }else{
                DB::table('historia_alumno')->insert([
                    'periodo'=>$periodo,
                    'no_de_control'=>$control,
                    'materia'=>$materia,
                    'grupo'=>$grupo,
                    'calificacion'=>$calificacion,
                    'tipo_evaluacion'=>$te,
                    'fecha_calificacion'=>Carbon::now(),
                    'plan_de_estudios'=>$plan,
                    'estatus_materia'=>'R',
                    'nopresento'=>null,
                    'usuario'=>$doc,
                    'fecha_actualizacion'=>null,
                    'periodo_acredita_materia'=>null,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>null
                ]);
            }
        }
        //Determino si es egresado
        $historial=$this->historial($control);
        $suma=0;
        $prom=0;
        $j=0;
        foreach ($historial as $historia){
            if($historia->calificacion>=60){
                $suma+=$historia->creditos_materia;
                if(($historia->calificacion>=70)&&(($historia->tipo_evaluacion!='AC')||($historia->tipo_evaluacion!='RC')||($historia->tipo_evaluacion!='RU'))){
                    $prom+=$historia->calificacion;
                }
            }
            $j++;
        }
        $promedio=($j==0)?0:round(($prom/$j),2);
        $totales=DB::table('carreras')
            ->where('carrera',$alumno->carrera)
            ->where('reticula',$alumno->reticula)
            ->select('creditos_totales')->first();
        if($suma>=$totales->creditos_totales){
            DB::table('alumnos')->where('no_de_control',$control)->update([
               'estatus_alumno'=>'EGR',
                'ultimo_periodo_inscrito'=>$periodo,
                'creditos_aprobados'=>$suma,
                'promedio_final_alcanzado'=>$promedio,
                'fecha_actualizacion'=>Carbon::now()
            ]);
        }
        return view('personal.residencias4');
    }
    public function actaresidencia($periodo,$materia,$grupo){
        $doc=session('docente');
        if(DB::table('seleccion_materias')->where('periodo',$periodo)
                ->where('materia',$materia)
                ->where('grupo',$grupo)
                ->count()>0){
            if(DB::table('seleccion_materias')->where('periodo',$periodo)
                    ->where('materia',$materia)
                    ->where('grupo',$grupo)
                    ->whereNotNull('calificacion')
                    ->count()>0){
                $inscritos=DB::table('seleccion_materias')->where('periodo',$periodo)
                    ->where('materia',$materia)
                    ->where('grupo',$grupo)
                    ->join('alumnos','seleccion_materias.no_de_control','=','alumnos.no_de_control')
                    ->join('tipos_evaluacion','seleccion_materias.tipo_evaluacion','=','tipos_evaluacion.tipo_evaluacion')
                    ->orderBy('apellido_paterno','asc')
                    ->orderBy('apellido_materno','asc')
                    ->orderBy('nombre_alumno','asc')
                    ->get();
                $datos=DB::table('grupos')->where('periodo',$periodo)
                    ->where('materia',$materia)
                    ->where('grupo',$grupo)
                    ->get();
                $nombre_mat=DB::table('materias')->where('materia',$materia)->get();
                $ndocente=Personal::where('correo_institucion',Auth::user()->email)
                    ->select('apellidos_empleado','nombre_empleado')->get();
                $nperiodo=DB::table('periodos_escolares')->where('periodo',$periodo)->get();
                $data=[
                    'alumnos'=>$inscritos,
                    'docente'=>$ndocente,
                    'nombre_periodo'=>$nperiodo,
                    'datos'=>$datos,
                    'nmateria'=>$nombre_mat,
                    'materia'=>$materia,
                    'grupo'=>$grupo
                ];
                $pdf = PDF::loadView('personal.pdf_acta', $data)
                    ->setPaper('Letter');
                return $pdf->download('acta.pdf');
            }else{
                $mensaje="Aún no cuenta con calificaciones registradas";
                return view('personal.no')->with(compact('mensaje'));
            }
        }else{
            $mensaje="No cuenta con alumnos inscritos en la materia";
            return view('personal.no')->with(compact('mensaje'));
        }
    }
}
