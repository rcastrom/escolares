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
    /*Termina para fichas */
}
