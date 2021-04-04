@extends('layouts.estudianhambre')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Estudiantes</div>
                <div class="card-body">
                    <h4 class="card-title">Consulta de boletas</h4>
                    <form method="post" action="{{route('alumno_boleta')}}" role="form" class="form-inline">
                        @csrf
                        <div class="form-group">
                            <label for="pbusqueda">Indique el período a buscar</label>
                            <select name="pbusqueda" id="pbusqueda" class="form-control" required>
                                <option value="" selected>--Seleccione--</option>
                                @foreach($periodos as $periodo)
                                    <option value="{{$periodo->periodo}}">{{$periodo->identificacion_corta}}</option>
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
