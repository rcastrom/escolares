@extends('layouts.academicos')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Jefaturas Académicas</div>
                <div class="card-body">
                    <h4 class="card-title">Materia: {{$nmateria->nombre_completo_materia}} Grupo: {{$grupo}}</h4>
                    <h5>Clave: {{$materia}}</h5>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Asignación de docente</div>
                <div class="card-body">
                    <form action="{{route('acad_altad')}}" method="post" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="control">Seleccione al docente asignado para la materia</label>
                            <select name="docente" id="docente" required class="form-control">
                                <option value="" selected>--Seleccione--</option>
                                <option value="999">Sin asignar</option>
                                @foreach($personal as $listado)
                                    <option value="{{$listado->rfc}}">
                                        {{$listado->apellidos_empleado}} {{$listado->nombre_empleado}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="materia" value="{{$materia}}">
                        <input type="hidden" name="grupo" value="{{$grupo}}">
                        <input type="hidden" name="periodo" value="{{$periodo}}">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Continuar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
