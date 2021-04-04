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
                    <div class="form-group">
                        <form action="{{route('escolares.accion_kardex_modificar1')}}" method="post" role="form">
                            @csrf
                            <label for="pbusqueda">Señale el período correspondiente para realizar la modificación a la materia</label>
                            <select name="pbusqueda" id="pbusqueda" required class="form-control">
                                <option value="" selected>--Seleccione</option>
                                @foreach($periodos as $periodo)
                                    <option value="{{$periodo->periodo}}">{{$periodo->identificacion_corta}}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="control" value="{{$control}}">
                            <button type="submit" class="btn btn-primary">Continuar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
