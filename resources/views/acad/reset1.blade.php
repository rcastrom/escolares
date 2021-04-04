@extends('layouts.academicos')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Jefaturas Académicas</div>
                <div class="card-body">
                    <p>Del siguiente listado, seleccione al docente que desea resetear el acta de calificación</p>
                    <form action="{{route('acad_reset2')}}" method="post" role="form">
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
                            <button type="submit" class="btn btn-primary">Continuar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
