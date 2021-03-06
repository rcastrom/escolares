@extends('layouts.sescolares')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Servicios Escolares</div>
                <div class="card-body">
                    <h4 class="card-title">{{ $alumno->apellido_paterno.' '.$alumno->apellido_materno.' '.$alumno->nombre_alumno }}</h4>
                        <p class="card-text">Número de control: {{ $control }}</p>
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
                    <?php $suma_total=0; $calificaciones_totales=0; $j=1; $tipos_mat=array("O2","R1","R2","RO","RP","2"); $tipos_aprob=array('AC','RC','RU','PG'); ?>
                    @foreach($calificaciones as $key=>$value)
                            @if(!empty($value))
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
                            $materias=1;
                            ?>
                            @foreach($value as $data)
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$data->nombre_completo_materia}}</td>
                                    <td>{{$data->calificacion <= 70 && in_array($data->tipo_evaluacion,$tipos_aprob)?'AC':($data->calificacion < 70?"NA":$data->calificacion)}}</td>
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
                                if($data->calificacion>=70||in_array($data->tipo_evaluacion,$tipos_aprob)){
                                    $suma_creditos+=$data->creditos_materia;
                                    if(!in_array($data->tipo_evaluacion,$tipos_aprob)){
                                        $cal_sem+=$data->calificacion;
                                        $calificaciones_totales+=$data->calificacion;
                                        $materias+=1;
                                        $j++;
                                    }
                                    $suma_total+=$data->creditos_materia;

                                }elseif($data->calificacion<70&&!in_array($data->tipo_evaluacion,$tipos_aprob)){
                                    $materias+=1;
                                }
                                    $suma_semestre+=$data->creditos_materia;
                                    $i++;
                                ?>
                            @endforeach
                            <?php $promedio=($materias-1)==0?0:round($cal_sem/($materias-1),2); ?>
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
                            @endif
                    @endforeach
                        <table class="table table-responsive">
                            <thead>
                            <tr>
                                <th>Porcentaje de avance</th>
                                <th>Promedio General</th>
                            </tr>
                            <tr>
                                <td align="center"><?php $avance=$suma_total==0?0:round(($suma_total/$ncarrera[0]->creditos_totales)*100,2); ?>{{$avance."%"}}</td>
                                <td align="center"><?php $prom_tot=($j-1)==0?0:round($calificaciones_totales/($j-1),2); ?>{{$prom_tot}}</td>
                            </tr>
                            </thead>
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
                    <form method="post" action="{{route('escolares.accion_kardex')}}" class="form-inline" role="form">
                        @csrf
                        <legend>Seleccione una opción</legend>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-6">
                                <label for="accion" class="sr-only">Acción</label>
                                <select name="accion" id="accion" required class="form-control">
                                    <option value="" selected>--Seleccione--</option>
                                    <option value="1">Agregar materia</option>
                                    <option value="2">Modificar materia</option>
                                    <option value="3">Imprimir</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Continuar</button>
                        <input type="hidden" name="control" id="control" value="{{ $control }}">
                    </form>
                </div>
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <p>Para modificar o eliminar una materia, el sistema le solicitará indique primeramente el período
                                    de la misma</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
