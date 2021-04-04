@extends('layouts.verano')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Coordinación Verano</div>
                <h5>Carrera {{$ncarrera[0]->nombre_reducido}} Reticula {{$ncarrera[0]->reticula}}</h5>
                <div class="card-body">
                    Del listado siguiente, seleccione el grupo a ser creado
                    <table class="table table-responsive">
                        <thead class="thead-light">
                            <tr>
                                <th>Semestre</th>
                                <th>Cve materia</th>
                                <th>Materia</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($listado as $grupos)
                            <tr>
                                <td>{{$grupos->semestre_reticula}}</td>
                                <td>{{$grupos->materia}}</td>
                                <td>{{$grupos->nombre_abreviado_materia}}</td>
                                <td><i class="far fa-question-circle"></i>
                                    <a href="/verano/grupos/alta/{{$grupos->materia}}/{{trim($carrera)}}/{{$ret}}"
                                       title="Obtener información">Crear</a></td>
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
