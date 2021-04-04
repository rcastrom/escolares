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
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Kardex de alumno</div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                        <form action="{{route('escolares.accion_kardex_alta')}}" method="post" role="form">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12 col-md-6">
                                    <label for="alta">Seleccione la materia por dar de alta</label>
                                    <select name="alta" id="alta" required class="form-control">
                                        <option value="" selected>--Indique--</option>
                                        @foreach($carga_acad as $materias)
                                            <option value="{{$materias->cve_mat}}">{{$materias->nmat}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <label for="calif">Indique la calificacion</label>
                                    <input type="number" name="calif" id="calif"  class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-6">
                                    <label for="nper">Seleccione el semestre de alta</label>
                                    <select name="nper" id="nper" required class="form-control">
                                        <option value="" selected>--Indique--</option>
                                        @foreach($periodos as $semestre)
                                            <option value="{{$semestre->periodo}}">{{$semestre->identificacion_corta}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <label for="tipo_e">Tipo de evaluación</label>
                                    <select name="tipo_e" id="tipo_e" required class="form-control">
                                        <option value="" selected>--Indique--</option>
                                        @foreach($tipo_ev as $te)
                                            <option value="{{$te->tipo_evaluacion}}">{{$te->descripcion_corta_evaluacion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Continuar</button>
                            <input type="hidden" name="control" id="control" value="{{ $control }}">
                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
