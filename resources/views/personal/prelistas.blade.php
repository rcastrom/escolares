@extends('layouts.docentes')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Personal Docente</div>
                <div class="card-body">
                    <h4 class="card-title">Período {{$nperiodo[0]->identificacion_larga}}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        @foreach($materias as $materia)
            <div class="col-sm-12 col-md-4">
                Clave materia {{$materia->materia}} Grupo {{$materia->grupo}}
            </div>
            <div class="col-sm-12 col-md-4">
                Nombre {{$materia->nombre_completo_materia}}
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="row">
                    <div class="col-sm-3">
                        <i class="fas fa-list-ol"></i>
                        <a href="/personal/semestre/listas/{{$materia->materia}}/{{$materia->grupo}}" title="Lista del semestre">
                            Obtener Listas PDF</a>
                    </div>
                    <div class="col-sm-3">
                        <i class="far fa-file-excel"></i>
                        <a href="/personal/semestre/excel/{{$materia->materia}}/{{$materia->grupo}}" title="Listas en Excel">
                            En Excel</a>
                    </div>
                    <div class="col-sm-3">
                        <i class="fas fa-sort-numeric-up"></i>
                        <a href="/personal/semestre/evaluar/{{$materia->materia}}/{{$materia->grupo}}" title="Calificaciones">Evaluar</a>
                    </div>
                    <div class="col-sm-3">
                        <i class="fas fa-print"></i>
                        <a href="/personal/semestre/acta/{{$materia->materia}}/{{$materia->grupo}}" title="Acta">Imprimir acta final</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
