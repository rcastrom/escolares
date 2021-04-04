@extends('layouts.sescolares')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Servicios Escolares</div>
                <div class="card-title">Liberación de Idioma Extranjero</div>
                <h4 class="card-info">Idioma: {{$lengua_extranjera->idiomas}}</h4>
                <h5 class="card-info">Estudiante: {{$alumno->apellido_paterno}} {{$alumno->apellido_materno}} {{$alumno->nombre_alumno}}</h5>
                <h5 class="card-info">Número de control {{$control}}</h5>
                <div class="card-body">
                    <form method="post" action="{{ route('escolares.liberar_idioma2') }}" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="opcion">Opción de liberación</label>
                            <select name="opcion" id="opcion" class="form-control" required>
                                <option value="" selected>--Seleccione--</option>
                                <option value="X">Examen</option>
                                <option value="A">Aprobación de curso</option>
                                <option value="D">Diplomado</option>
                                <option value="E">Institución Externa</option>
                            </select>
                        </div>
                        <input type="hidden" name="control" value="{{$control}}">
                        <input type="hidden" name="idioma" value="{{$idioma}}">
                        <button type="submit" class="btn btn-primary">Continuar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
