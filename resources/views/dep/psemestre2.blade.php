@extends('layouts.division')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo División de Estudios</div>
                <div class="card-body">
                    <h4 class="card-title">Nombre {{$alumno->nombre_alumno}} {{$alumno->apellido_paterno}} {{$alumno->apellido_materno}}</h4>
                    <h5 class="card-title">Número de control {{$control}}</h5>
                    <h5 class="card-title">Carrera actual {{$ncarrera->nombre_reducido}} retícula {{$alumno->reticula}}</h5>
                    <h5 class="card-title">Período de ingreso {{$alumno->periodo_ingreso_it}}</h5>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{route('division_cambio_primer2')}}" method="post" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="destino">Carrera destino del estudiante</label>
                            <select name="destino" id="destino" required class="form-control">
                                <option value="" selected>--Seleccione--</option>
                                @foreach($carreras as $carrera)
                                    <option value="{{$carrera->carrera.'_'.$carrera->reticula}}">{{$carrera->nombre_reducido.' RET('.$carrera->reticula.')'}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="grupo">Grupo a ser asignado</label>
                            <input type="text" name="grupo" id="grupo" required onchange="this.value=this.value.toUpperCase();" maxlength="3" class="form-control">
                        </div>
                        <input type="hidden" name="control" id="control" value="{{$control}}">
                        <button type="submit" class="btn btn-primary">Continuar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
