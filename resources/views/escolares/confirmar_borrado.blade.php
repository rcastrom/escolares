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
                    <div class="card bg-danger text-white">
                        <div class="card-body">¡ATENCIÓN!</div>
                        <div class="card-body">
                            Éste módulo se encarga de eliminar el número de control para {{$alumno->no_de_control}}, por lo que
                            se borrará su historial académico y demás información.
                        </div>
                    </div>
                    <form action="{{route('escolares.accion_borrar')}}" method="post">
                        @csrf
                        <div class="form-group">
                            <input type="checkbox" value="1" name="confirmar" required class="form-check-inline">Favor de confirmar
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-danger">¿Continuar?</button>
                            <input type="hidden" name="control" id="control" value="{{$control}}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
