@extends('layouts.sescolares')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Servicios Escolares</div>
                <div class="card-body">
                    <h4 class="card-title">Actas del período {{$nperiodo->identificacion_corta}}</h4>
                    <br>
                    <h5 class="card-title">Docente {{$ndocente->apellidos_empleado}} {{$ndocente->nombre_empleado}}</h5>
                    <br>
                    @foreach($grupos as $grupo)
                        <div class="row">
                            <div class="col-sm-3 col-md-3">
                                {{$grupo->nombre_abreviado_materia}}
                            </div>
                            <div class="col-sm-3 col-md-3">
                                Grupo {{$grupo->grupo}}
                            </div>
                            <div class="col-sm-3 col-md-3">
                                <i class="fas fa-wrench"></i>
                                <a href="/escolares/acta/modificar/{{$periodo}}/{{$docente}}/{{$grupo->materia}}/{{$grupo->grupo}}" title="Modificar">
                                    Modificar</a>
                            </div>
                            <div class="col-sm-3 col-md-3">
                                <i class="fas fa-print"></i>
                                <a href="/escolares/acta/imprimir/{{$periodo}}/{{$docente}}/{{$grupo->materia}}/{{$grupo->grupo}}" title="Modificar">
                                Imprimir</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
