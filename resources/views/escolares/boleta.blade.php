@extends('layouts.sescolares')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Servicios Escolares</div>
                <div class="card-body">
                    <h4 class="card-title">{{ $alumno->apellido_paterno.' '.$alumno->apellido_materno.' '.$alumno->nombre_alumno }}</h4>
                        <p class="card-text">Número de control: {{ $alumno->no_de_control }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Boleta del período {{$nombre_periodo[0]->identificacion_larga}}</div>
                <div class="card-body">
                    <?php
                    $tipos_mat=array("O2","R1","R2","RO","RP");
                    $i=1;
                    $suma_creditos=0;
                    $promedio_semestre=0;
                    $suma_semestre=0;
                    $cal_sem=0;
                    ?>
                        <table class="table table-responsive table-bordered">
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
                            @foreach($cal_periodo as $data)
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
                                }
                                    $suma_semestre+=$data->creditos_materia;
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
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Datos Adicionales</div>
                <div class="card-body">
                    <form method="post" action="{{route('escolares.imprimir boleta')}}" class="form-inline" role="form">
                        @csrf
                        <legend>Seleccione una opción</legend>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-6">
                                <label for="accion" class="sr-only">Acción</label>
                                <select name="accion" id="accion" required class="form-control">
                                    <option value="" selected>--Seleccione--</option>
                                    <option value="1">Imprimir</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Continuar</button>
                        <input type="hidden" name="control" id="control" value="{{ $alumno->no_de_control }}">
                        <input type="hidden" name="periodo" id="periodo" value="{{ $periodo }}">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
