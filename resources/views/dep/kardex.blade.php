@extends('layouts.division')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo División de Estudios</div>
                <div class="card-body">
                    <h4 class="card-title">{{ $alumno->apellido_paterno.' '.$alumno->apellido_materno.' '.$alumno->nombre_alumno }}</h4>
                        <p class="card-text">Número de control: {{ $control }}</p>
                        <p class="card-text">{{ $ncarrera[0]->nombre_carrera }} Ret {{ $alumno->reticula }}</p>
                        <p class="card-text">Semestre {{ $alumno->semestre }}</p>
                        <p class="card-text">Especialidad {{ $especialidad }}</p>
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
                                    <td>{{$data->nombre_completo_materia}}</td>
                                    <td>{{$data->calificacion<60?"NA":($data->tipo_evaluacion=='AC'?'AC':$data->calificacion)}}</td>
                                    <td>{{$data->descripcion_corta_evaluacion}}</td>
                                    <td>{{$data->creditos_materia}}</td>
                                    @if(($data->calificacion < 70 && in_array($data->tipo_evaluacion,$tipos_mat)) || ($data->calificacion < 70 && $data->tipo_evaluacion == 'EA')){
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
                                if($data->calificacion>=70||($data->tipo_evaluacion=='AC')){
                                    $suma_creditos+=$data->creditos_materia;
                                    $cal_sem+=$data->calificacion;
                                    $calificaciones_totales+=$data->calificacion;
                                }
                                $suma_semestre+=$data->creditos_materia;
                                $i++;
                                ?>
                            @endforeach
                            <?php $promedio=($i-1)==0?0:round($cal_sem/($i-1),2); ?>
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
