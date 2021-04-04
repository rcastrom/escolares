@extends('layouts.estudianhambre')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Estudiantes</div>
                <div class="card-body">
                    <h4 class="card-title">{{ $alumno->apellido_paterno.' '.$alumno->apellido_materno.' '.$alumno->nombre_alumno }}</h4>
                        <p class="card-text">Número de control: {{ $alumno->no_de_control }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-sm-12 col-md-12">
            <div class="card">
                <div class="card-header">Calificaciones del periodo {{ $nperiodo->identificacion_corta }}</div>
                <div class="card-body">
                    <table class="table table-responsive">
                        <thead class="thead-light">
                        <tr>
                            <th>Materia</th>
                            <th>Grupo</th>
                            <th>Calificación</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($carga as $value)
                            <tr>
                                <td>{{$value->nombre_abreviado_materia}}</td>
                                <td>{{$value->grupo}}</td>
                                <td>{{$value->calificacion}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
