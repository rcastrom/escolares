<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlaneacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(){
        return view('planeacion.inicio');
    }
    public function periodo(){
        $periodo_actual=Db::Select('select periodo from pac_periodo_actual()');
        return $periodo_actual;
    }
    public function inscritos($periodo){
        $data=DB::select("select * from pac_poblacion('$periodo')");
        return $data;
    }
    public function semreal($periodo_ingreso,$periodo){
        $anio_actual=substr($periodo, 0, 4);
 		$anio_ingresa=substr($periodo_ingreso, 0, 4);
 		$tipo_ingreso=substr($periodo_ingreso,-1);
        $per=substr($periodo,-1);
        if($per=="3"){
            $semestre=($tipo_ingreso=='3')?(2*($anio_actual-$anio_ingresa)+1):(2*($anio_actual-$anio_ingresa)+2);
        }else{
            $semestre=($tipo_ingreso=='3')?(2*($anio_actual-$anio_ingresa)):(2*($anio_actual-$anio_ingresa)+1);
        }
		return $semestre;
    }
    public function edad($periodo,$genero){
        $data=DB::select("select * from pac_edades('$periodo','$genero')");
        return $data;
    }
    public function edad_carrera($periodo,$genero,$carrera,$reticula){
        $data=DB::select("select * from pac_edades_carrera('$periodo','$genero','$carrera','$reticula')");
        return $data;
    }
    public function edad_estado($periodo,$genero,$carrera,$reticula,$estado){
        $data=DB::select("select * from pac_edades_estado('$periodo','$genero','$carrera','$reticula','$estado')");
        return $data;
    }
    public function prepoblacion(){
        $periodos=DB::table('periodos_escolares')->orderBy('periodo','desc')->get();
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        return view('planeacion.prepoblacion')->with(compact('periodos','periodo'));
    }
    public function preedades(){
        $periodos=DB::table('periodos_escolares')->orderBy('periodo','desc')->get();
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        return view('planeacion.preedades')->with(compact('periodos','periodo'));
    }
    public function preedadesc(){
        $periodos=DB::table('periodos_escolares')->orderBy('periodo','desc')->get();
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        $carreras=DB::table('carreras')->select('carrera','reticula','nombre_reducido')->get();
        return view('planeacion.preedadesc')->with(compact('periodos','periodo','carreras'));
    }
    public function preedadesedo(){
        $periodos=DB::table('periodos_escolares')->orderBy('periodo','desc')->get();
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        $carreras=DB::table('carreras')->select('carrera','reticula','nombre_reducido')->get();
        $estados=DB::table('entidades_federativas')->orderBy('entidad_federativa')->get();
        return view('planeacion.preedadesedo')->with(compact('periodos','periodo','carreras','estados'));
    }
    public function preegreso(){
        $periodos=DB::table('periodos_escolares')->orderBy('periodo','desc')->get();
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        $carreras=DB::table('carreras')->select('carrera','reticula','nombre_reducido')->get();
        return view('planeacion.preegreso')->with(compact('periodos','periodo','carreras'));
    }
    public function preegreso2(){
        $periodos=DB::table('periodos_escolares')->orderBy('periodo','desc')->get();
        $periodo_actual=$this->periodo();
        $periodo=$periodo_actual[0]->periodo;
        $carreras=DB::table('carreras')->select('carrera','reticula','nombre_reducido')->get();
        return view('planeacion.preegreso2')->with(compact('periodos','periodo','carreras'));
    }
    public function poblacion(Request $request){
        $periodo=$request->get('periodo');
        $inscritos=$this->inscritos($periodo);
        $nperiodo=DB::table('periodos_escolares')->where('periodo',$periodo)->first();
        return view('planeacion.poblacion')->with(compact('inscritos','periodo','nperiodo'));
    }
    public function edades(Request $request){
        $periodo=$request->get('periodo');
        $age_h=$this->edad($periodo,'M');
        $age_m=$this->edad($periodo,'F');
        $hombres=array_fill(1,16,array_fill(1,20,0));
        $mujeres=array_fill(1,16,array_fill(1,20,0));
        $edades=array("1"=>"Menos de 18 ","2"=>"18 ","3"=>"19 ","4"=>"20 ","5"=>"21 ","6"=>"22 ","7"=>"23 ",
            "8"=>"24 ","9"=>"25 ","10"=>"26 ","11"=>"27 ","12"=>"28 ","13"=>"29 ","14"=>"30 a 34 ",
            "15"=>"35 a 39 ","16"=>"Mas de 39 ");
        foreach ($age_h as $key=>$value){
            $sem=$value->semestre;
            $age=$value->edad;
            if($sem<=9){
                $ubica=2*$sem-1;
                switch($age){
                    case ($age<18):$hombres[1][$ubica]++; break;
                    case 18:$hombres[2][$ubica]++; break;
                    case 19:$hombres[3][$ubica]++; break;
                    case 20:$hombres[4][$ubica]++; break;
                    case 21:$hombres[5][$ubica]++; break;
                    case 22:$hombres[6][$ubica]++; break;
                    case 23:$hombres[7][$ubica]++; break;
                    case 24:$hombres[8][$ubica]++; break;
                    case 25:$hombres[9][$ubica]++; break;
                    case 26:$hombres[10][$ubica]++; break;
                    case 27:$hombres[11][$ubica]++; break;
                    case 28:$hombres[12][$ubica]++; break;
                    case 29:$hombres[13][$ubica]++; break;
                    case (($age>=30)&&($age<=34)):$hombres[14][$ubica]++; break;
                    case (($age>=35)&&($age<=39)):$hombres[15][$ubica]++; break;
                    case ($age>=40):$hombres[16][$ubica]++; break;
                }
            }else{
                switch($age){
                    case ($age<18):$hombres[1][19]++; break;
                    case 18:$hombres[2][19]++; break;
                    case 19:$hombres[3][19]++; break;
                    case 20:$hombres[4][19]++; break;
                    case 21:$hombres[5][19]++; break;
                    case 22:$hombres[6][19]++; break;
                    case 23:$hombres[7][19]++; break;
                    case 24:$hombres[8][19]++; break;
                    case 25:$hombres[9][19]++; break;
                    case 26:$hombres[10][19]++; break;
                    case 27:$hombres[11][19]++; break;
                    case 28:$hombres[12][19]++; break;
                    case 29:$hombres[13][19]++; break;
                    case (($age>=30)&&($age<=34)):$hombres[14][19]++; break;
                    case (($age>=35)&&($age<=39)):$hombres[15][19]++; break;
                    case ($age>=40):$hombres[16][19]++; break;
                }
            }
        }
        foreach ($age_m as $key=>$value){
            $sem=$value->semestre;
            $age=$value->edad;
            if($sem<=9){
                $ubica=2*$sem;
                switch($age){
                    case ($age<18):$mujeres[1][$ubica]++; break;
                    case 18:$mujeres[2][$ubica]++; break;
                    case 19:$mujeres[3][$ubica]++; break;
                    case 20:$mujeres[4][$ubica]++; break;
                    case 21:$mujeres[5][$ubica]++; break;
                    case 22:$mujeres[6][$ubica]++; break;
                    case 23:$mujeres[7][$ubica]++; break;
                    case 24:$mujeres[8][$ubica]++; break;
                    case 25:$mujeres[9][$ubica]++; break;
                    case 26:$mujeres[10][$ubica]++; break;
                    case 27:$mujeres[11][$ubica]++; break;
                    case 28:$mujeres[12][$ubica]++; break;
                    case 29:$mujeres[13][$ubica]++; break;
                    case (($age>=30)&&($age<=34)):$mujeres[14][$ubica]++; break;
                    case (($age>=35)&&($age<=39)):$mujeres[15][$ubica]++; break;
                    case ($age>=40):$mujeres[16][$ubica]++; break;
                }
            }else{
                switch($age){
                    case ($age<18):$mujeres[1][20]++; break;
                    case 18:$mujeres[2][20]++; break;
                    case 19:$mujeres[3][20]++; break;
                    case 20:$mujeres[4][20]++; break;
                    case 21:$mujeres[5][20]++; break;
                    case 22:$mujeres[6][20]++; break;
                    case 23:$mujeres[7][20]++; break;
                    case 24:$mujeres[8][20]++; break;
                    case 25:$mujeres[9][20]++; break;
                    case 26:$mujeres[10][20]++; break;
                    case 27:$mujeres[11][20]++; break;
                    case 28:$mujeres[12][20]++; break;
                    case 29:$mujeres[13][20]++; break;
                    case (($age>=30)&&($age<=34)):$mujeres[14][20]++; break;
                    case (($age>=35)&&($age<=39)):$mujeres[15][20]++; break;
                    case ($age>=40):$mujeres[16][20]++; break;
                }
            }
        }
        $nperiodo=DB::table('periodos_escolares')->where('periodo',$periodo)->first();
        return view('planeacion.edades')->with(compact('periodo','nperiodo','edades','hombres','mujeres'));
    }
    public function edadesc(Request $request){
        $periodo=$request->get('periodo');
        $carr=$request->get('carrera');
        $data0=explode("_",$carr);
        $carrera=$data0[0]; $reticula=$data0[1];
        $age_h=$this->edad_carrera($periodo,'M',$carrera,$reticula);
        $age_m=$this->edad_carrera($periodo,'F',$carrera,$reticula);
        $hombres=array_fill(1,16,array_fill(1,20,0));
        $mujeres=array_fill(1,16,array_fill(1,20,0));
        $edades=array("1"=>"Menos de 18 ","2"=>"18 ","3"=>"19 ","4"=>"20 ","5"=>"21 ","6"=>"22 ","7"=>"23 ",
            "8"=>"24 ","9"=>"25 ","10"=>"26 ","11"=>"27 ","12"=>"28 ","13"=>"29 ","14"=>"30 a 34 ",
            "15"=>"35 a 39 ","16"=>"Mas de 39 ");
        foreach ($age_h as $key=>$value){
            $sem=$value->semestre;
            $age=$value->edad;
            if($sem<=9){
                $ubica=2*$sem-1;
                switch($age){
                    case ($age<18):$hombres[1][$ubica]++; break;
                    case 18:$hombres[2][$ubica]++; break;
                    case 19:$hombres[3][$ubica]++; break;
                    case 20:$hombres[4][$ubica]++; break;
                    case 21:$hombres[5][$ubica]++; break;
                    case 22:$hombres[6][$ubica]++; break;
                    case 23:$hombres[7][$ubica]++; break;
                    case 24:$hombres[8][$ubica]++; break;
                    case 25:$hombres[9][$ubica]++; break;
                    case 26:$hombres[10][$ubica]++; break;
                    case 27:$hombres[11][$ubica]++; break;
                    case 28:$hombres[12][$ubica]++; break;
                    case 29:$hombres[13][$ubica]++; break;
                    case (($age>=30)&&($age<=34)):$hombres[14][$ubica]++; break;
                    case (($age>=35)&&($age<=39)):$hombres[15][$ubica]++; break;
                    case ($age>=40):$hombres[16][$ubica]++; break;
                }
            }else{
                switch($age){
                    case ($age<18):$hombres[1][19]++; break;
                    case 18:$hombres[2][19]++; break;
                    case 19:$hombres[3][19]++; break;
                    case 20:$hombres[4][19]++; break;
                    case 21:$hombres[5][19]++; break;
                    case 22:$hombres[6][19]++; break;
                    case 23:$hombres[7][19]++; break;
                    case 24:$hombres[8][19]++; break;
                    case 25:$hombres[9][19]++; break;
                    case 26:$hombres[10][19]++; break;
                    case 27:$hombres[11][19]++; break;
                    case 28:$hombres[12][19]++; break;
                    case 29:$hombres[13][19]++; break;
                    case (($age>=30)&&($age<=34)):$hombres[14][19]++; break;
                    case (($age>=35)&&($age<=39)):$hombres[15][19]++; break;
                    case ($age>=40):$hombres[16][19]++; break;
                }
            }
        }
        foreach ($age_m as $key=>$value){
            $sem=$value->semestre;
            $age=$value->edad;
            if($sem<=9){
                $ubica=2*$sem;
                switch($age){
                    case ($age<18):$mujeres[1][$ubica]++; break;
                    case 18:$mujeres[2][$ubica]++; break;
                    case 19:$mujeres[3][$ubica]++; break;
                    case 20:$mujeres[4][$ubica]++; break;
                    case 21:$mujeres[5][$ubica]++; break;
                    case 22:$mujeres[6][$ubica]++; break;
                    case 23:$mujeres[7][$ubica]++; break;
                    case 24:$mujeres[8][$ubica]++; break;
                    case 25:$mujeres[9][$ubica]++; break;
                    case 26:$mujeres[10][$ubica]++; break;
                    case 27:$mujeres[11][$ubica]++; break;
                    case 28:$mujeres[12][$ubica]++; break;
                    case 29:$mujeres[13][$ubica]++; break;
                    case (($age>=30)&&($age<=34)):$mujeres[14][$ubica]++; break;
                    case (($age>=35)&&($age<=39)):$mujeres[15][$ubica]++; break;
                    case ($age>=40):$mujeres[16][$ubica]++; break;
                }
            }else{
                switch($age){
                    case ($age<18):$mujeres[1][20]++; break;
                    case 18:$mujeres[2][20]++; break;
                    case 19:$mujeres[3][20]++; break;
                    case 20:$mujeres[4][20]++; break;
                    case 21:$mujeres[5][20]++; break;
                    case 22:$mujeres[6][20]++; break;
                    case 23:$mujeres[7][20]++; break;
                    case 24:$mujeres[8][20]++; break;
                    case 25:$mujeres[9][20]++; break;
                    case 26:$mujeres[10][20]++; break;
                    case 27:$mujeres[11][20]++; break;
                    case 28:$mujeres[12][20]++; break;
                    case 29:$mujeres[13][20]++; break;
                    case (($age>=30)&&($age<=34)):$mujeres[14][20]++; break;
                    case (($age>=35)&&($age<=39)):$mujeres[15][20]++; break;
                    case ($age>=40):$mujeres[16][20]++; break;
                }
            }
        }
        $nperiodo=DB::table('periodos_escolares')->where('periodo',$periodo)->first();
        $ncarrera=DB::table('carreras')->where('carrera',$carrera)->where('reticula',$reticula)->first();
        return view('planeacion.edadesc')->with(compact('periodo','nperiodo','edades','hombres','mujeres','ncarrera','reticula'));
    }
    public function edadesedo(Request $request){
        $periodo=$request->get('periodo');
        $carr=$request->get('carrera');
        $data0=explode("_",$carr);
        $carrera=$data0[0]; $reticula=$data0[1];
        $edades=array("1"=>"Menos de 18 ","2"=>"18 ","3"=>"19 ","4"=>"20 ","5"=>"21 ","6"=>"22 ","7"=>"23 ",
            "8"=>"24 ","9"=>"25 ","10"=>"26 ","11"=>"27 ","12"=>"28 ","13"=>"29 ","14"=>"30 a 34 ",
            "15"=>"35 a 39 ","16"=>"Mas de 39 ");
        $nperiodo=DB::table('periodos_escolares')->where('periodo',$periodo)->first();
        $ncarrera=DB::table('carreras')->where('carrera',$carrera)->where('reticula',$reticula)->first();
        $estados=DB::table('entidades_federativas')->select('clave_entidad','nombre_entidad')->get();
        return view('planeacion.edadesedo')->with(compact('periodo','nperiodo','ncarrera','carrera','reticula','edades','estados'));
    }
    public function egreso(Request $request){
        $periodo_inicio=$request->get('periodo1');
        $periodo_fin=$request->get('periodo2');
        $estudio=$request->get('estudio');
        $tipo=$request->get('tipo');
        $hombres=array_fill(1,7,0);
        $mujeres=array_fill(1,7,0);
        $edades=array("1"=>"Menor o igual a 21 ","2"=>"22 ","3"=>"23 ","4"=>"24 ","5"=>"25 ","6"=>"26 a 29 ","7"=>"30 o más");
        if($periodo_inicio==1){
            if($estudio==1){
                if($tipo=="E"){
                    $age_h=DB::select("SELECT date_part('year',current_date)-date_part('year',fecha_nacimiento) as edad FROM alumnos WHERE estatus_alumno='EGR' AND ultimo_periodo_inscrito='$periodo_fin' and sexo='M'");
                    $age_m=DB::select("SELECT date_part('year',current_date)-date_part('year',fecha_nacimiento) as edad FROM alumnos WHERE estatus_alumno='EGR' AND ultimo_periodo_inscrito='$periodo_fin' and sexo='F'");
                }else{
                    $age_h=DB::select("SELECT date_part('year',current_date)-date_part('year',fecha_nacimiento) as edad FROM alumnos WHERE estatus_alumno='EGR' AND periodo_titulacion='$periodo_fin' and sexo='M'");
                    $age_m=DB::select("SELECT date_part('year',current_date)-date_part('year',fecha_nacimiento) as edad FROM alumnos WHERE estatus_alumno='EGR' AND periodo_titulacion='$periodo_fin' and sexo='F'");
                }
            }else{
                $data=explode("_",$estudio);
                $carrera=$data[0]; $ret=$data[1];
                if($tipo=="E"){
                    $age_h=DB::select("SELECT date_part('year',current_date)-date_part('year',fecha_nacimiento) as edad FROM alumnos WHERE estatus_alumno='EGR' AND carrera='$carrera' AND reticula='$ret' AND ultimo_periodo_inscrito='$periodo_fin' and sexo='M'");
                    $age_m=DB::select("SELECT date_part('year',current_date)-date_part('year',fecha_nacimiento) as edad FROM alumnos WHERE estatus_alumno='EGR' AND carrera='$carrera' AND reticula='$ret' AND ultimo_periodo_inscrito='$periodo_fin' and sexo='F'");
                }else{
                    $age_h=DB::select("SELECT date_part('year',current_date)-date_part('year',fecha_nacimiento) as edad FROM alumnos WHERE estatus_alumno='EGR' AND carrera='$carrera' AND reticula='$ret' AND periodo_titulacion='$periodo_fin' and sexo='M'");
                    $age_m=DB::select("SELECT date_part('year',current_date)-date_part('year',fecha_nacimiento) as edad FROM alumnos WHERE estatus_alumno='EGR' AND carrera='$carrera' AND reticula='$ret' AND periodo_titulacion='$periodo_fin' and sexo='F'");
                }
            }
        }else{
            if($estudio==1){
                if($tipo=="E"){
                    $age_h=DB::select("SELECT date_part('year',current_date)-date_part('year',fecha_nacimiento) as edad FROM alumnos WHERE estatus_alumno='EGR' AND ultimo_periodo_inscrito='$periodo_fin' and sexo='M'");
                    $age_m=DB::select("SELECT date_part('year',current_date)-date_part('year',fecha_nacimiento) as edad FROM alumnos WHERE estatus_alumno='EGR' AND ultimo_periodo_inscrito='$periodo_fin' and sexo='F'");
                }else{
                    $age_h=DB::select("SELECT date_part('year',current_date)-date_part('year',fecha_nacimiento) as edad FROM alumnos WHERE estatus_alumno='EGR' AND periodo_titulacion='$periodo_fin' and sexo='M'");
                    $age_m=DB::select("SELECT date_part('year',current_date)-date_part('year',fecha_nacimiento) as edad FROM alumnos WHERE estatus_alumno='EGR' AND periodo_titulacion='$periodo_fin' and sexo='F'");
                }
            }else{
                $data=explode("_",$estudio);
                $carrera=$data[0]; $ret=$data[1];
                if($tipo=="E"){
                    $age_h=DB::select("SELECT date_part('year',current_date)-date_part('year',fecha_nacimiento) as edad FROM alumnos WHERE estatus_alumno='EGR' AND carrera='$carrera' AND reticula='$ret' AND ultimo_periodo_inscrito='$periodo_fin' and sexo='M'");
                    $age_m=DB::select("SELECT date_part('year',current_date)-date_part('year',fecha_nacimiento) as edad FROM alumnos WHERE estatus_alumno='EGR' AND carrera='$carrera' AND reticula='$ret' AND ultimo_periodo_inscrito='$periodo_fin' and sexo='F'");
                }else{
                    $age_h=DB::select("SELECT date_part('year',current_date)-date_part('year',fecha_nacimiento) as edad FROM alumnos WHERE estatus_alumno='EGR' AND carrera='$carrera' AND reticula='$ret' AND periodo_titulacion='$periodo_fin' and sexo='M'");
                    $age_m=DB::select("SELECT date_part('year',current_date)-date_part('year',fecha_nacimiento) as edad FROM alumnos WHERE estatus_alumno='EGR' AND carrera='$carrera' AND reticula='$ret' AND periodo_titulacion='$periodo_fin' and sexo='F'");
                }
            }
        }
        foreach ($age_h as $key=>$value){
            $age=$value->edad;
                switch($age){
                    case ($age<=21):$hombres[1]++; break;
                    case 22:$hombres[2]++; break;
                    case 23:$hombres[3]++; break;
                    case 24:$hombres[4]++; break;
                    case 25:$hombres[5]++; break;
                    case (($age>=26)&&($age<=29)):$hombres[6]++; break;
                    case ($age>=30):$hombres[7]++; break;
                }
        }
        foreach ($age_m as $key=>$value){
            $age=$value->edad;
                switch($age){
                    case ($age<=21):$mujeres[1]++; break;
                    case 22:$mujeres[2]++; break;
                    case 23:$mujeres[3]++; break;
                    case 24:$mujeres[4]++; break;
                    case 25:$mujeres[5]++; break;
                    case (($age>=26)&&($age<=29)):$mujeres[6]++; break;
                    case ($age>=30):$mujeres[7]++; break;
                }
        }
        $tconsulta=$tipo=="E"?"Egresados":"Titulados";
        if($estudio=="1"){
            $ncarrera="En general";
        }else{
            $data=explode("_",$estudio);
            $carrera=$data[0]; $ret=$data[1];
            $ncarrera1=DB::table('carreras')->where('carrera',$carrera)
                ->where('reticula',$ret)->select('nombre_reducido')->first();
            $ncarrera=$ncarrera1->nombre_reducido." Ret ".$ret;
        }
        $nperiodo_fin=DB::table('periodos_escolares')->where('periodo',$periodo_fin)
            ->select('identificacion_corta')->first();
        return view('planeacion.edadesegreso')->with(compact('edades','hombres','mujeres','tconsulta','ncarrera','nperiodo_fin'));
    }
    public function egreso2(Request $request){
        $periodo_inicio=$request->get('periodo1');
        $periodo_fin=$request->get('periodo2');
        $estudio=$request->get('estudio');
        $tipo=$request->get('tipo');
        //Primero, por egresados
        if($tipo=="E"){
            if($estudio==1){
                $data_h=DB::select("SELECT COUNT(no_de_control) AS egresados, A.carrera,nombre_carrera FROM alumnos A,carreras C WHERE estatus_alumno='EGR' and ultimo_periodo_inscrito='$periodo_fin' and sexo='M' and A.carrera=C.carrera GROUP BY A.carrera,nombre_carrera");
                $data_m=DB::select("SELECT COUNT(no_de_control) AS egresados, A.carrera,nombre_carrera FROM alumnos A,carreras C WHERE estatus_alumno='EGR' and ultimo_periodo_inscrito='$periodo_fin' and sexo='F' and A.carrera=C.carrera GROUP BY A.carrera,nombre_carrera");
                $data_t=DB::select("SELECT COUNT(no_de_control) AS egresados, A.carrera,nombre_carrera FROM alumnos A,carreras C WHERE estatus_alumno='EGR' and ultimo_periodo_inscrito='$periodo_fin' and A.carrera=C.carrera GROUP BY A.carrera,nombre_carrera");
            }else{
                $data_h=DB::select("SELECT COUNT(no_de_control) AS egresados, A.carrera,nombre_carrera FROM alumnos A,carreras C WHERE estatus_alumno='EGR' and ultimo_periodo_inscrito='$periodo_fin' and A.carrera='$estudio' and sexo='M' and A.carrera=C.carrera GROUP BY A.carrera,nombre_carrera");
                $data_m=DB::select("SELECT COUNT(no_de_control) AS egresados, A.carrera,nombre_carrera FROM alumnos A,carreras C WHERE estatus_alumno='EGR' and ultimo_periodo_inscrito='$periodo_fin' and A.carrera='$estudio' and sexo='F' and A.carrera=C.carrera GROUP BY A.carrera,nombre_carrera");
                $data_t=DB::select("SELECT COUNT(no_de_control) AS egresados, A.carrera,nombre_carrera FROM alumnos A,carreras C WHERE estatus_alumno='EGR' and ultimo_periodo_inscrito='$periodo_fin' and A.carrera='$estudio' and A.carrera=C.carrera GROUP BY A.carrera,nombre_carrera");
            }
        }else{
            if($estudio==1){
                $data_h=DB::select("SELECT COUNT(no_de_control) AS egresados, A.carrera,nombre_carrera FROM alumnos A,carreras C WHERE estatus_alumno='EGR' and ultimo_periodo_inscrito='$periodo_fin' and periodo_titulacion!='null' and sexo='M' and A.carrera=C.carrera GROUP BY A.carrera,nombre_carrera");
                $data_m=DB::select("SELECT COUNT(no_de_control) AS egresados, A.carrera,nombre_carrera FROM alumnos A,carreras C WHERE estatus_alumno='EGR' and ultimo_periodo_inscrito='$periodo_fin' and periodo_titulacion!='null' and sexo='F' and A.carrera=C.carrera GROUP BY A.carrera,nombre_carrera");
                $data_t=DB::select("SELECT COUNT(no_de_control) AS egresados, A.carrera,nombre_carrera FROM alumnos A,carreras C WHERE estatus_alumno='EGR' and ultimo_periodo_inscrito='$periodo_fin' and periodo_titulacion!='null' and A.carrera=C.carrera GROUP BY A.carrera,nombre_carrera");
            }else{
                $data_h=DB::select("SELECT COUNT(no_de_control) AS egresados, A.carrera,nombre_carrera FROM alumnos A,carreras C WHERE estatus_alumno='EGR' and ultimo_periodo_inscrito='$periodo_fin' and periodo_titulacion!='null' and A.carrera='$estudio' and sexo='M' and A.carrera=C.carrera GROUP BY A.carrera,nombre_carrera");
                $data_m=DB::select("SELECT COUNT(no_de_control) AS egresados, A.carrera,nombre_carrera FROM alumnos A,carreras C WHERE estatus_alumno='EGR' and ultimo_periodo_inscrito='$periodo_fin' and periodo_titulacion!='null' and A.carrera='$estudio' and sexo='F' and A.carrera=C.carrera GROUP BY A.carrera,nombre_carrera");
                $data_t=DB::select("SELECT COUNT(no_de_control) AS egresados, A.carrera,nombre_carrera FROM alumnos A,carreras C WHERE estatus_alumno='EGR' and ultimo_periodo_inscrito='$periodo_fin' and periodo_titulacion!='null' and A.carrera='$estudio' and A.carrera=C.carrera GROUP BY A.carrera,nombre_carrera");
            }
        }
        $tconsulta=$tipo=="E"?"Egresados":"Titulados";
        $nperiodo_fin=DB::table('periodos_escolares')->where('periodo',$periodo_fin)
            ->select('identificacion_corta')->first();
        return view('planeacion.egreso')->with(compact('data_h','data_m','data_t','tconsulta','nperiodo_fin'));
    }
    public function pobxcarrera($periodo,$carrera,$reticula){
        $nperiodo=DB::table('periodos_escolares')->where('periodo',$periodo)->first();
        $ncarrera=DB::table('carreras')->where('carrera',$carrera)->where('reticula',$reticula)->first();
        $hombres=array_fill(1,10,0);
        $mujeres=array_fill(1,10,0);
        $semestres=array(1=>"1",2=>"2",3=>"3",4=>"4",5=>"5",6=>"6",7=>"7",8=>"8",9=>"9",10=>">9");
        $pob_masc=DB::table('seleccion_materias')->where('periodo',$periodo)
            ->join('alumnos','alumnos.no_de_control','=','seleccion_materias.no_de_control')
            ->where('carrera',$carrera)
            ->where('reticula',$reticula)
            ->where('sexo','M')
            ->select('seleccion_materias.no_de_control','periodo_ingreso_it')
            ->distinct()
            ->get();
        $pob_fem=DB::table('seleccion_materias')->where('periodo',$periodo)
            ->join('alumnos','alumnos.no_de_control','=','seleccion_materias.no_de_control')
            ->where('carrera',$carrera)
            ->where('reticula',$reticula)
            ->where('sexo','F')
            ->select('seleccion_materias.no_de_control','periodo_ingreso_it')
            ->distinct()
            ->get();
        foreach ($pob_masc as $key=>$value){
            $periodo_ingreso=$value->periodo_ingreso_it;
            $semestre=$this->semreal($periodo_ingreso,$periodo);
            switch ($semestre){
                case 1: $hombres[1]++; break;
                case 2: $hombres[2]++; break;
                case 3: $hombres[3]++; break;
                case 4: $hombres[4]++; break;
                case 5: $hombres[5]++; break;
                case 6: $hombres[6]++; break;
                case 7: $hombres[7]++; break;
                case 8: $hombres[8]++; break;
                case 9: $hombres[9]++; break;
                case ($semestre>9): $hombres[10]++; break;
            }
        }
        foreach ($pob_fem as $key=>$value){
            $periodo_ingreso=$value->periodo_ingreso_it;
            $semestre=$this->semreal($periodo_ingreso,$periodo);
            switch ($semestre){
                case 1: $mujeres[1]++; break;
                case 2: $mujeres[2]++; break;
                case 3: $mujeres[3]++; break;
                case 4: $mujeres[4]++; break;
                case 5: $mujeres[5]++; break;
                case 6: $mujeres[6]++; break;
                case 7: $mujeres[7]++; break;
                case 8: $mujeres[8]++; break;
                case 9: $mujeres[9]++; break;
                case ($semestre>9): $mujeres[10]++; break;
            }
        }
        return view('planeacion.poblacion2')->with(compact('semestres','hombres','mujeres','ncarrera','reticula','nperiodo'));
    }
    public function materianueva(){
        $carreras=DB::table('carreras')->select('carrera','reticula','nombre_reducido')->get();
        return view('planeacion.materias_alta')->with(compact('carreras'));
    }
    public function materiasacciones(Request $request){
        $accion=$request->get('accion');
        $carr=$request->get('carrera');
        $datos=explode("_",$carr);
        $carrera=trim($datos[0]); $reticula=$datos[1];
        if($accion==1){
            $espe=DB::table('especialidades')->where('carrera',$carrera)->where('reticula',$reticula)->get();
            $ncarrera=DB::table('carreras')->where('carrera',$carrera)->where('reticula',$reticula)
                ->first();
            return view('planeacion.reticulas')->with(compact('carrera','reticula','espe','ncarrera'));
        }
    }
    public function vistareticula(Request $request){
        $carrera=$request->get('carrera');
        $reticula=$request->get('reticula');
        $especialidad=$request->get('espe');
        $materias=DB::table('materias_carreras')
            ->where('carrera',$carrera)->where('reticula',$reticula)
            ->join('materias','materias_carreras.materia','=','materias.materia')
            ->where(function ($query) use ($especialidad){
                $query->whereNull('especialidad')
                    ->orWhere('especialidad','=',$especialidad);
            })
            ->select('materias_carreras.materia','nombre_abreviado_materia','creditos_materia','horas_teoricas','horas_practicas','semestre_reticula','renglon')
            ->get();
        $espe=DB::table('especialidades')->where('carrera',$carrera)
            ->where('reticula',$reticula)->where('especialidad',$especialidad)
            ->first();
        $ncarrera=DB::table('carreras')->where('carrera',$carrera)->where('reticula',$reticula)
            ->first();
        return view('planeacion.reticula_vista')->with(compact('espe','materias','ncarrera'));
    }
    public function contrasenia(){
        return view('planeacion.contrasenia');
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
        return view('planeacion.inicio');
    }

}
