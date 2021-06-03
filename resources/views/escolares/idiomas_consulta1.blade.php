@extends('layouts.sescolares')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Servicios Escolares</div>
                <div class="card-body">
                    <p>El siguiente módulo, es para consultar el listado de cursos de lengua
                    extranjera impartidos y registrados en la Institución.
                    </p>
                    <form method="post" action="{{route('escolares.cursos_idiomas')}}" role="form">
                        @csrf
                        <legend>Cursos impartidos Lengua Extranjera</legend>
                            <div class="form-group">
                                <label for="periodo"> Indique el período de consulta </label>
                                <select name="periodo" id="periodo" class="form-control" required>
                                    @foreach($periodos as $per)
                                        <option value="{{$per->periodo}}" {{$per->periodo==$periodo?" selected":""}}>{{$per->identificacion_corta}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="idioma"> Seleccione la lengua extranjera de consulta </label>
                                <select name="idioma" id="idioma" class="form-control" required>
                                    @foreach($idiomas as $leng_ext)
                                        <option value="{{$leng_ext->id}}">{{$leng_ext->idiomas}}</option>
                                    @endforeach
                                </select>
                            </div>
                        <button type="submit" class="btn btn-primary">Continuar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
