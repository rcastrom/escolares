@extends('layouts.sescolares')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Servicios Escolares</div>
                <div class="card-body">
                    <h4 class="card-title">{{ $alumno->apellido_paterno.' '.$alumno->apellido_materno.' '.$alumno->nombre_alumno }}</h4>
                    <p class="card-text">Número de control: {{ $control }}</p>
                    <p class="card-text">Período {{$nombre_periodo->identificacion_corta}}</p>
                    <form action="{{route('escolares.accion_actualiza_estatus')}}" method="post" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="estatus">Cambio de estatus de alumno</label>
                            <select name="estatus" id="estatus" class="form-control">
                                @foreach($estatus_alumno as $status)
                                    <option value="{{$status->estatus}}"{{$status->estatus==$alumno->estatus_alumno?' selected':''}}>{{$status->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="periodo" value="{{$periodo}}">
                        <input type="hidden" name="control" value="{{$control}}">
                        <button type="submit" class="btn btn-primary">Continuar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
