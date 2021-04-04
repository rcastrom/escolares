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
                    <h5>Liberación de idioma extranjero</h5>
                    <form action="{{route('escolares.idiomas')}}" method="post" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="fexpedicion">Fecha de expedición de la constancia</label>
                            <input type="date" value="{{date('Y-m-d')}}" name="fexpedicion" class="form-control">
                        </div>
                        <input type="hidden" name="control" value="{{$control}}">
                        <button type="submit" class="btn btn-primary">Continuar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
