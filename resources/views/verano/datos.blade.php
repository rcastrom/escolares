@extends('layouts.verano')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Verano</div>
                <div class="card-body">
                    <h4 class="card-title">{{ $alumno->apellido_paterno.' '.$alumno->apellido_materno.' '.$alumno->nombre_alumno }}</h4>
                        <p class="card-text">Número de control: {{ $id }}</p>
                        <p class="card-text">{{ $ncarrera[0]->nombre_carrera }} Ret {{ $alumno->reticula }}</p>
                        <p class="card-text">Semestre {{ $alumno->semestre }}</p>
                        <p class="card-text">Estatus actual: {{ $estatus[0]->descripcion }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Datos Adicionales</div>
                <div class="card-body">
                    <form method="post" action="{{ route('verano.accion2') }}" class="form-inline" role="form">
                        @csrf
                        <legend>Seleccione una opción</legend>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-6">
                                <label for="accion" class="sr-only">Acción</label>
                                <select name="accion" id="accion" required class="form-control">
                                    <option value="" selected>--Seleccione--</option>
                                    <option value="1">Kárdex</option>
                                    <option value="2">Retícula</option>
                                    <option value="3">Horario</option>
                                    <option value="4">Validar reinscripción</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Continuar</button>
                        <input type="hidden" name="control" id="control" value="{{ $id }}">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
