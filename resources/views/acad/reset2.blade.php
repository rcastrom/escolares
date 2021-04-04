@extends('layouts.academicos')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Jefaturas Académicas</div>
                <div class="card-body">
                    <p>Seleccione la materia cuya acta será borrada (reseteada) para vuelta a captura por
                        parte del docente.</p>
                    <h4>Docente: {{$doc->apellidos_empleado." ".$doc->nombre_empleado}}</h4>
                    <form action="{{route('acad_reset3')}}" method="post" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="salon">Materias del docente</label>
                            <select name="materia" id="materia" class="form-control" required>
                                <option value="" selected>--Seleccione--</option>
                                @foreach($materias as $mat)
                                    <option value="{{$mat->materia."_".$mat->grupo}}">{{$mat->nombre_abreviado_materia.'/Gpo '.$mat->grupo}}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="rfc" value="{{$rfc}}">
                        <input type="hidden" name="periodo" value="{{$periodo}}">
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
