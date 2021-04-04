@extends('layouts.academicos')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Jefaturas Académicas</div>
                <div class="card-body">
                    <p>Del siguiente listado, seleccione al docente que desea tener mayores detalles</p>
                    <form action="{{route('acad_personal')}}" method="post" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="salon">Buscar por docente</label>
                            <select name="rfc" id="rfc" class="form-control" required>
                                <option value="" selected>--Seleccione--</option>
                                @foreach($personal as $doc)
                                    <option value="{{$doc->rfc}}">{{$doc->apellidos_empleado.' '.$doc->nombre_empleado}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="periodo" class="col-form-label">Período</label>
                            <select name="periodo" id="periodo" class="form-control">
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
