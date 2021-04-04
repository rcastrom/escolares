@extends('layouts.verano')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Coordinación Verano</div>
                <h5>Carrera {{$ncarrera[0]->nombre_reducido}} Reticula {{$ncarrera[0]->reticula}}</h5>
                <div class="card-body">
                    Del listado siguiente, seleccione el grupo para obtener mayor información.
                    <table class="table table-responsive">
                        <thead class="thead-light">
                            <tr>
                                <th>Semestre</th>
                                <th>Materia</th>
                                <th>Grupo</th>
                                <th>Paralelo de</th>
                                <th>Inscritos</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($listado as $grupos)
                            <tr>
                                <td>{{$grupos->semestre_reticula}}</td>
                                <td>Materia: {{$grupos->nombre_abreviado_materia}} Cve: {{$grupos->materia}}</td>
                                <td>{{$grupos->grupo}}</td>
                                <td>{{$grupos->paralelo_de}}</td>
                                <td>{{$grupos->alumnos_inscritos}}</td>
                                <td><i class="far fa-question-circle"></i>
                                    <a href="/verano/grupos/info/{{$grupos->materia}}/{{$grupos->grupo}}"
                                       title="Obtener información">Mayor información</a></td>
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
