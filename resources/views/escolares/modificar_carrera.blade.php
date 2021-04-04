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
                    <form action="{{route('escolares.accion_actualiza_carrera')}}" method="post" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="carrera_n">Cambiar a la carrera (convalidación) de alumno</label>
                            <select name="carrera_n" id="carrera_n" class="form-control" required>
                                <option value="" selected>--Seleccione--</option>
                                @foreach($carreras as $carrera)
                                    <option value="{{trim($carrera->carrera)."_".$carrera->reticula}}">{{$carrera->nombre_carrera}} (RET {{$carrera->reticula}})</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="control" value="{{$control}}">
                        <button type="submit" class="btn btn-primary">Continuar</button>
                    </form>
                </div>
                <div class="row">
                    <div class="card bg-danger text-white">
                        <div class="card-body">PRECAUCIÓN</div>
                        <div class="card-body">
                            Se eliminarán las materias que no sean compatibles con el cambio de carrera, por lo que siempre
                            debe contarse con un kárdex de respaldo
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
