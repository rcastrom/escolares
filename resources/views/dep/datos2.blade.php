@extends('layouts.division')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo División Estudios</div>
                <div class="card-body">
                    <div class="row">
                        <form method="post" action="{{ route('dep.accion2') }}" class="form-inline" role="form">
                            @csrf
                            <legend>Acción a realizar</legend>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6">
                                    <label for="control">Seleccione al estudiante</label>
                                    <select name="control" id="control" required class="form-control">
                                        <option value="" selected>--Seleccione</option>
                                        @foreach($arroja as $datos)
                                            <option value='{{ "$datos->no_de_control" }}'>
                                                {{ strval($datos->no_de_control)." ".$datos->apellido_paterno.' '.$datos->apellido_materno.
                                                    ' '.$datos->nombre_alumno}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6">
                                    <label for="accion"> Accion a realizar </label>
                                    <select name="accion" id="accion" required class="form-control">
                                        <option value="" selected>--Seleccione--</option>
                                        <option value="1">Kárdex</option>
                                        <option value="2">Retícula</option>
                                        <option value="3">Horario</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Continuar</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
