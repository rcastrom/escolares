@extends('layouts.sescolares')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @foreach($materias as $mater)
                    <?php
                        $semestre_reticula = $mater->semestre_reticula;
                        $renglon = $mater->renglon;
                        $array_reticula[$renglon][$semestre_reticula]['clave'] = $mater->materia;
                        $array_reticula[$renglon][$semestre_reticula]['materia'] = $mater->nombre_abreviado_materia;
                        $array_reticula[$renglon][$semestre_reticula]['creditos_materia'] = $mater->creditos_materia;
                        $array_reticula[$renglon][$semestre_reticula]['horas_teoricas'] = $mater->horas_teoricas;
                        $array_reticula[$renglon][$semestre_reticula]['horas_practicas'] = $mater->horas_practicas;
                    ?>
                    @endforeach
                        <br>
                        <h4 class="card-title">{{$ncarrera->nombre_reducido}} Ret {{$ncarrera->reticula}}</h4>
                        <h4 class="card-title">Especialidad {{$espe->nombre_especialidad}}</h4>
                    <table align="center" border="1" bordercolor="#000000">
                        <tr>
                            @for($i=1; $i<=10; $i++)
                                <th class="medium_center">Semestre<br>{{$i}}</th>
                            @endfor
                        </tr>
                        @for($renglon=1; $renglon<=8; $renglon++)
                            <tr>
                            @for($semestre=1; $semestre<=10; $semestre++)
                                <?php
                                    if(isset($array_reticula[$renglon][$semestre])){
                                        $materia = $array_reticula[$renglon][$semestre]['materia'];
                                        $clave = $array_reticula[$renglon][$semestre]['clave'];
                                        $horas_teoricas = $array_reticula[$renglon][$semestre]['horas_teoricas'];
                                        $horas_practicas = $array_reticula[$renglon][$semestre]['horas_practicas'];
                                        $creditos_materia = $array_reticula[$renglon][$semestre]['creditos_materia'];
                                        $bandera=1;
                                    }else{
                                        $bandera=0;
                                    }
                                ?>
                                @if($bandera)
                                    <td align="center" height="80" width="90" class="small_center azul">
                                        {{$materia}}<br>{{$clave}}<br>
                                        {{$horas_teoricas}}-{{$horas_practicas}}-{{$creditos_materia}}
                                    </td>
                                @else
                                     <td align="center" height="80" width="90" class="small_center"></td>
                                @endif
                            @endfor
                            </tr>
                        @endfor
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
