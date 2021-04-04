@extends('layouts.sescolares')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Servicios Escolares</div>
                <div class="card-body">
                    <h4 class="card-title">{{$alumno->apellido_paterno}} {{$alumno->apellido_materno}} {{$alumno->nombre_alumno}}</h4>
                    <h4 class="card-title">{{$control}}</h4>
                    <h4 class="card-title">Período {{$nperiodo->identificacion_corta}}</h4>
                    @foreach($mat as $materias)
                        <div class="row">
                            <div class="col-sm-4 col-md-4">
                                {{$materias->nombre_abreviado_materia}}
                            </div>
                            <div class="col-sm-3 col-md-2">
                                {{$materias->calificacion}}
                            </div>
                            <div class="col-sm-3 col-md-2">
                                {{$materias->tipo_evaluacion}}/{{$materias->descripcion_corta_evaluacion}}
                            </div>
                            <div class="col-sm-2 col-md-4">
                                <i class="fas fa-wrench"></i>
                                <a href="/escolares/modificar/{{$periodo}}/{{$control}}/{{$materias->materia}}" title="Modificar">
                                    Modificar</a>
                                <i class="fas fa-trash-alt"></i>
                                <a href="/escolares/eliminar/{{$periodo}}/{{$control}}/{{$materias->materia}}" title="Eliminar">
                                    Eliminar</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
