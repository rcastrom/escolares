@extends('layouts.sescolares')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Servicios Escolares</div>
                <div class="card-body">
                    <h4 class="card-title">{{ $alumno->apellido_paterno.' '.$alumno->apellido_materno.' '.$alumno->nombre_alumno }}</h4>
                    <p class="card-text">Número de control: {{ $alumno->no_de_control }}</p>
                    <div class="card bg-info text-white">
                        <div class="card-body">Baja temporal</div>
                        <div class="card-body">
                            Éste módulo se encarga de realizar la baja temporal o definitiva para el número de control {{$alumno->no_de_control}}
                        </div>
                    </div>
                    <form action="{{route('escolares.accion_bajatemp')}}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="tbaja">Señale el tipo de baja a realizar</label>
                            <select name="tbaja" id="tbaja" required class="form-control">
                                <option value="" selected>--Seleccione--</option>
                                <option value="BT1">Baja temporal</option>
                                <option value="BDG">Baja definitiva</option>
                                <option value="BDE">Baja definitiva por emisión de certificado parcial</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">¿Continuar?</button>
                            <input type="hidden" name="control" id="control" value="{{$alumno->no_de_control}}">
                            <input type="hidden" name="periodo" id="periodo" value="{{$periodo}}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
