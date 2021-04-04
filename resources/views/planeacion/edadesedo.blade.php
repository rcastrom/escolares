@extends('layouts.planeacion')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Módulo Planeación</div>
                <div class="card-body">
                    <h3>Edades de alumnos x carrera (911) Período {{$nperiodo->identificacion_corta}}</h3>
                    <h4>Carrera {{$ncarrera->nombre_reducido}} retícula {{$reticula}}</h4>
                    @foreach($estados as $key=>$value)
                        <h5>Entidad Federativa {{$value->nombre_entidad}}</h5>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <td></td>
                                <th colspan='20' align="center">Grado de avance (semestre)</th>
                            </tr>
                            <tr>
                                <td></td>
                                @for($i=1;$i<=10;$i++)
                                    @if($i<=9)
                                        <td align="center" colspan="2">{{$i}}</td>
                                    @else
                                        <td colspan="2">>9</td>
                                    @endif
                                @endfor
                            </tr>
                            <tr>
                                <td>Edad</td>
                                @for($i=1;$i<=20;$i++)
                                    @if($i%2==0)
                                        <td>M</td>
                                    @else
                                        <td>H</td>
                                    @endif
                                @endfor
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $estado=$value->clave_entidad;
                                $age_h=Illuminate\Support\Facades\DB::select("select * from pac_edades_estado('$periodo','M','$carrera','$reticula','$estado')");
                                $age_m=Illuminate\Support\Facades\DB::select("select * from pac_edades_estado('$periodo','F','$carrera','$reticula','$estado')");
                                $hombres=array_fill(1,16,array_fill(1,20,0));
                                $mujeres=array_fill(1,16,array_fill(1,20,0));
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
                            ?>
                            @foreach($edades as $key=>$value)
                                <tr>
                                    <td>{{$value}} años</td>
                                    @for($i=1;$i<=20;$i++)
                                        @if($i%2==0)
                                            <td>{{$mujeres[$key][$i]}}</td>
                                        @else
                                            <td>{{$hombres[$key][$i]}}</td>
                                        @endif
                                    @endfor
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
