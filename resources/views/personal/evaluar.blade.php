@extends('layouts.docentes')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Personal Docente</div>
                <div class="card-body">
                    <h4 class="card-title">Período {{$nperiodo[0]->identificacion_larga}}</h4>
                    <h5 class="card-title">Materia {{$nombre_mat[0]->nombre_completo_materia}} Grupo {{$grupo}}</h5>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form name="calificaciones" method="post" action="{{route('personal_cal1')}}" role="form" class="form">
                        @csrf
                        @foreach($inscritos as $alumnos)
                            <div class="form-group row">
                                <label for="{{$alumnos->no_de_control}}" class="col-sm-4 col-md-8 col-form-label">
                                    {{$alumnos->no_de_control}}
                                    {{$alumnos->apellido_paterno}} {{$alumnos->apellido_materno}} {{$alumnos->nombre_alumno}}
                                </label>
                                <input type="number" id="{{$materia."_".$alumnos->no_de_control}}"
                                       name="{{$materia."_".$alumnos->no_de_control}}" value="0" class="col-sm-4 col-md-2 form-control">
                                <select name="{{"op_".$alumnos->no_de_control}}" id="{{"op_".$alumnos->no_de_control}}"
                                        class="col-sm-4 col-md-2 form-control">
                                    <option value="1" selected>Oportunidad 1</option>
                                    <option value="2" >Oportunidad 2</option>
                                </select>
                            </div>
                        @endforeach
                        <input type="hidden" name="materia" value="{{$materia}}">
                        <input type="hidden" name="grupo" value="{{$grupo}}">
                        <div class="row">
                            <button type="submit" class="btn btn-primary">Continuar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
