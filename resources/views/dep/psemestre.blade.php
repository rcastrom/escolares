@extends('layouts.division')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo División de Estudios</div>
                <div class="card-body">
                    <h4 class="card-title">Cambio de carrera alumnos primer semestre</h4>
                    <p>El módulo se emplea para cambiar de carrera a un estudiante de primer semestre del período
                    en curso, asignando a su vez la carga académica correspondiente</p>
                    <p>No se eliminará el número de control que el estudiante tenga asignado, solamente se cambiará
                    hacia la carrera que señale posteriormente como destino, con la carga académica correspondiente</p>
                    <p>Se toma en referencia como carga académica aquellas materias que en el sistema están
                    registradas en 1er semestre</p>
                    <p>La fecha límite para realizar el cambio es asignada por Servicios Escolares</p>
                    <p>La actualización en el número de inscritos por materia se realizará posteriormente</p>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{route('division_cambio_primer')}}" method="post" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="control">Indique el número de control del estudiante</label>
                            <input type="text" name="control" id="control" required class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Continuar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
