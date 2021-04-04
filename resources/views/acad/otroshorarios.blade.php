@extends('layouts.academicos')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Jefaturas Académicas</div>
                <div class="card-body">
                    <p>Seleccione la acción correspondiente</p>
                    <form action="{{route('acad_otrosh')}}" method="post" role="form" >
                        @csrf
                        <div class="form-group">
                            <label for="rfc">Seleccione al personal</label>
                            <select name="rfc" id="rfc" required class="form-control">
                                <option value="" selected>--Seleccione--</option>
                                @foreach($personal as $docente)
                                    <option value="{{$docente->rfc}}">{{$docente->apellidos_empleado}} {{$docente->nombre_empleado}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="accion">Accion a realizar</label>
                            <select name="accion" id="accion" required class="form-control">
                                <option value="" selected>--Seleccione--</option>
                                <option value="1">Alta horario administrativo</option>
                                <option value="2">Modificación horario administrativo</option>
                                <option value="3">Alta horario apoyo</option>
                                <option value="4">Modificación horario apoyo</option>
                                <option value="5">Alta observaciones para horario</option>
                                <option value="6">Modificación observaciones para horario</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="periodo">Periodo</label>
                            <select name="periodo" id="periodo" required class="form-control">
                                @foreach($periodos as $per)
                                    <option value="{{$per->periodo}}"{{$per->periodo==$periodo?' selected':''}}>{{$per->identificacion_corta}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Continuar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
