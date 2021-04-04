@extends('layouts.sescolares')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Servicios Escolares</div>
                <div class="card-body">
                    <h4 class="card-title">{{ $alumno->apellido_paterno.' '.$alumno->apellido_materno.' '.$alumno->nombre_alumno }}</h4>
                        <p class="card-text">Número de control: {{ $id }}</p>
                        <p class="card-text">{{ $ncarrera[0]->nombre_carrera }} Ret {{ $alumno->reticula }}</p>
                        <p class="card-text">Período de ingreso {{ $ingreso->identificacion_corta }}</p>
                        <p class="card-text">Semestre {{ $alumno->semestre }}</p>
                        <p class="card-text">Especialidad: {{ $especialidad}}</p>
                        <p class="card-text">Estatus actual: {{ $estatus[0]->descripcion }}</p>
                        <p class="card-text">NIP: {{ $alumno->nip }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Datos Generales</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">Domicilio</div>
                        <div class="col-sm-12 col-md-6">Calle {{ $datos->domicilio_calle }} Colonia
                            {{ $datos->domicilio_colonia }} C.P. {{ $datos->codigo_postal }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6">Telefono</div>
                        <div class="col-sm-12 col-md-6">{{ $datos->telefono }} </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6">Correo</div>
                        <div class="col-sm-12 col-md-6">{{ $alumno->correo_electronico }} </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6">NSS</div>
                        <div class="col-sm-12 col-md-6">{{ $alumno->nss }} </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6">CURP</div>
                        <div class="col-sm-12 col-md-6">{{ $alumno->curp_alumno }} </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Datos Adicionales</div>
                <div class="card-body">
                    <form method="post" action="{{ route('escolares.accion') }}" class="form-inline" role="form">
                        @csrf
                        <legend>Seleccione una opción</legend>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-6">
                                <label for="accion" class="sr-only">Acción</label>
                                <select name="accion" id="accion" required class="form-control">
                                    <option value="" selected>--Seleccione--</option>
                                    <option value="1">Kárdex</option>
                                    <option value="2">Retícula</option>
                                    <option value="3">Constancia de Estudios</option>
                                    <option value="4">Boleta</option>
                                    <option value="5">Horario</option>
                                    <option value="6">Cambiar estatus</option>
                                    <option value="7">Validar reinscripción</option>
                                    <option value="8">Asignar especialidad</option>
                                    <option value="9">Cambio carrera</option>
                                    <option value="10">Eliminar número de control</option>
                                    <option value="11">Baja temporal o definitiva</option>
                                    <option value="12">Asignación de NSS</option>
                                    <option value="13">Acreditar complementaria</option>
                                    <option value="14">Liberación idioma extranjero</option>
                                    <option value="15">Certificado</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-12 col-md-6">
                                <label for="periodo" class="sr-only">Periodo</label>
                                <select name="periodo" id="periodo" required class="form-control">
                                    @foreach($periodos as $pers)
                                        @if($periodo[0]->periodo==$pers->periodo)
                                            <option value="{{$pers->periodo}}" selected>{{$pers->identificacion_corta}}</option>
                                        @else
                                            <option value="{{$pers->periodo}}">{{$pers->identificacion_corta}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Continuar</button>
                        <div class="row">
                            <div class="col-sm-12 col-md-12">
                                El período puede no ser necesario dependiendo de la acción a realizar
                            </div>
                        </div>
                        <input type="hidden" name="control" id="control" value="{{ $id }}">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
