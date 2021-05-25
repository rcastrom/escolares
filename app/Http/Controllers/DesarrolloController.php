<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class DesarrolloController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(){
        return view('desarrollo.inicio');
    }
    /*Para fichas*/
    public function fichas_inicio(){
        $periodos=DB::table('periodos_escolares')->get();
        $periodo_ficha=DB::table('parametros_fichas')
            ->where('activo','1')->first();
        return view('desarrollo.fichas_inicio')->with(compact('periodos','periodo_ficha'));
    }
    public function fichas_inicio_parametros(Request $request){
        $periodo_ficha=$request->get('per_ficha');
        $periodo_original=$request->get('periodo');
        $entrega=$request->get('entrega');
        $termina=$request->get('termina');
        $inicio_prope=$request->get('inicio_prope');
        $fin_prope=$request->get('fin_prope');
        if($periodo_original==$periodo_ficha){
            DB::table('parametros_fichas')->where('fichas',$periodo_ficha)
                ->update([
                   'activo'=>'1',
                   'inicio_prope'=>$inicio_prope,
                   'fin_prope'=>$fin_prope,
                    'entrega'=>$entrega,
                    'termina'=>$termina
                ]);
        }else{
            if(DB::table('parametros_fichas')->where('fichas',$periodo_ficha)->count()>0){
                DB::table('parametros_fichas')->where('fichas',$periodo_ficha)
                    ->update([
                        'activo'=>'1',
                        'inicio_prope'=>$inicio_prope,
                        'fin_prope'=>$fin_prope,
                        'entrega'=>$entrega,
                        'termina'=>$termina
                    ]);
                if(DB::table('parametros_fichas')
                        ->where('fichas',$periodo_original)
                        ->count()>0){
                    DB::table('parametros_fichas')->where('fichas',$periodo_original)
                        ->update([
                            'activo'=>'0',
                        ]);
                }else{
                    DB::table('parametros_fichas')->insert([
                        'fichas'=>$periodo_original,
                        'activo'=>'0',
                        'fin_prope'=>$fin_prope,
                        'entrega'=>$entrega,
                        'termina'=>$termina
                    ]);
                }
            }else{
                DB::table('parametros_fichas')->insert([
                    'fichas'=>$periodo_ficha,
                    'activo'=>'1',
                    'fin_prope'=>$fin_prope,
                    'entrega'=>$entrega,
                    'termina'=>$termina
                ]);
            }
        }
        return view('desarrollo.si');
    }
    public function fichas_carreras(){
        $periodo_ficha=DB::table('parametros_fichas')->where('activo','1')->first();
        $nperiodo=DB::table('periodos_escolares')->where('periodo',$periodo_ficha->fichas)->first();
        $carreras=DB::table('carreras')->orderBy('nombre_carrera','ASC')
            ->where('nivel_escolar','L')
            ->orderBy('reticula','ASC')->get();
        return view('desarrollo.fichas_carreras1')->with(compact('carreras','nperiodo'));
    }
    public function fichas_carreras_actualizar(Request $request){
        //Primero, se elimina lo ya existente para poder actualizar
        DB::table('carreras')->where('ofertar','1')->update([
           'ofertar'=>'0'
        ]);
        //Ahora, se actualiza
        foreach ($request->get('carreras') as $value){
            $data=explode("_",$value);
            $carrera=trim($data[0]); $reticula=$data[1];
            DB::table('carreras')->where('carrera',$carrera)->where('reticula',$reticula)
                ->update([
                    'ofertar'=>'1'
                ]);
        }
        return view('desarrollo.si');
    }
    public function fichas_aulas_mostrar(){
        $aulas=DB::table('aulas')->where('estatus','A')
            ->select('aula','capacidad')->get();
        $carreras=DB::table('carreras')->where('ofertar','1')
            ->orderBy('nombre_reducido','ASC')
            ->get();
        $periodo_ficha=DB::table('parametros_fichas')->where('activo','1')->first();
        $nperiodo=DB::table('periodos_escolares')->where('periodo',$periodo_ficha->fichas)->first();
        if(DB::table('aulas_aspirantes')->count()>0){
            $registros=DB::table('aulas_aspirantes')->join('carreras','aulas_aspirantes.carrera','=','carreras.carrera')
                ->where('carreras.ofertar','1')
                ->select('nombre_reducido','aula','capacidad','disponibles')->get();
            $bandera=1;
            return view('desarrollo.fichas_aulas1')->with(compact('aulas','carreras','nperiodo','bandera','registros'));
        }else{
            $bandera=0;
            return view('desarrollo.fichas_aulas1')->with(compact('aulas','carreras','nperiodo','bandera'));
        }
    }
    public function fichas_aulas_actualizar(Request $request){
        $request->validate([
            'salon'=>'unique:aulas_aspirantes,aula',
            'cupo'=>'required|numeric|min:1'
        ],[
            'salon.unique'=>'El aula que está señalando ya está siendo usada',
            'cupo.required'=>'Por favor, asigne un cupo al aula señalada',
            'cupo.numeric'=>'El cupo debe ser un valor numérico',
            'cupo.min'=>'El cupo no puede ser negativo ni cero'
        ]);
        DB::table('aulas_aspirantes')->insert([
            'aula'=>$request->salon,
            'capacidad'=>$request->cupo,
            'disponibles'=>$request->cupo,
            'carrera'=>$request->carrera
        ]);
        return redirect()->route('fichas_carreras');
    }
    /*Termina para fichas */
}
