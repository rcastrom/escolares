@extends('layouts.sescolares')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Servicios Escolares</div>
                <div class="card-body">
                    <form action="{{route('escolares.accion-reinscripcion')}}" method="post" role="form">
                        @csrf
                        <div class="form-group row">
                            <label for="periodo" class="col-sm-4 col-form-label">Período</label>
                            <div class="col-sm-8">
                                <select name="periodo" id="periodo" class="form-control">
                                    @foreach($periodos as $per)
                                        <option value="{{$per->periodo}}"{{$per->periodo==$periodo?' selected':''}}>{{$per->identificacion_corta}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="carrera" class="col-sm-4 col-form-label">Carrera</label>
                            <div class="col-sm-8">
                                <select name="carrera" id="carrera" class="form-control" required>
                                    <option value="" selected>--Seleccione--</option>
                                    @foreach($carreras as $carrera)
                                        <option value="{{$carrera->carrera}}">{{$carrera->nombre_reducido}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="accion" class="col-sm-4 col-form-label">Acción</label>
                            <div class="col-sm-8">
                                <select name="accion" id="accion" class="form-control" required>
                                    <option value="" selected>--Seleccione--</option>
                                    <option value="1">Establecer fechas de inscripción</option>
                                    <option value="2">Generar lista inscripción</option>
                                    <option value="3">Imprimir lista orden inscripción</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-8">
                                <button type="submit" class="btn btn-primary">Continuar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
