@extends('layouts.verano')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Verano</div>
                <div class="card-body">
                    <h4 class="card-title">{{ $alumno->apellido_paterno.' '.$alumno->apellido_materno.' '.$alumno->nombre_alumno }}</h4>
                        <p class="card-text">Número de control: {{ $alumno->no_de_control }}</p>
                        <p class="card-text">{{ $ncarrera[0]->nombre_carrera }} Ret {{ $alumno->reticula }}</p>
                        <p class="card-text">Semestre {{ $alumno->semestre }}</p>
                        <p class="card-text">Estatus actual: {{ $estatus[0]->descripcion }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Kardex de alumno</div>
                <div class="card-body">
                    <?php $suma_total=0; $calificaciones_totales=0; $tipos_mat=array("O2","R1","R2","RO","RP"); ?>
                    @foreach($calificaciones as $key=>$value)
                        <caption>{{$nperiodos[$key][0]->identificacion_larga}}</caption>
                        <table class="table table-responsive table-striped">
                            <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Materia</th>
                                <th>Calificación</th>
                                <th>Oportunidad</th>
                                <th>Créditos</th>
                                <th>Observaciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $i=1;
                            $suma_creditos=0;
                            $promedio_semestre=0;
                            $suma_semestre=0;
                            $cal_sem=0;
                            ?>
                            @foreach($value as $data)
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$data->nombre}}</td>
                                    <td>{{$data->cal<60?"NA":($data->tipo=='AC'?'AC':$data->cal)}}</td>
                                    <td>{{$data->descripcion}}</td>
                                    <td>{{$data->credit}}</td>
                                    @if(($data->cal < 70 && in_array($data->tipo,$tipos_mat)) || ($data->cal < 70 && $data->tipo == 'EA')){
                                        @if($alumno->plan_de_estudios==3||$alumno->plan_de_estudios==4){
                                            <td>A curso especial</td>
                                        }
                                        @else{
                                           <td></td>
                                        }
                                        @endif
                                    @endif
                                </tr>
                                <?php
                                if($data->cal>=70||($data->tipo=='AC')){
                                    $suma_creditos+=$data->credit;
                                    $cal_sem+=$data->cal;
                                    $calificaciones_totales+=$data->cal;
                                }
                                    $suma_semestre+=$data->credit;
                                    $i++;
                                ?>
                            @endforeach
                            <?php $promedio=round($cal_sem/($i-1),2); ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>Créditos Aprobados/Solicitados</td>
                                    <td>{{$suma_creditos}}/{{$suma_semestre}}</td>
                                    <td>Promedio del semestre</td>
                                    <td>{{$promedio}}</td>
                                </tr>
                            </tfoot>
                        </table>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
