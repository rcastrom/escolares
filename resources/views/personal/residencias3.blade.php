@extends('layouts.docentes')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Personal Docente</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h3>Evaluación residencias profesionales</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h4>No. control {{$control}}</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>{{$alumno->apellido_paterno}} {{$alumno->apellido_materno}} {{$alumno->nombre_alumno}}</h5>
                        </div>
                    </div>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{route('personal_residencias2')}}" method="post" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="calificacion">Calificación</label>
                            <input type="number" name="calificacion" id="calificacion" required class="form-control" min="0" max="100">
                        </div>
                        <input type="hidden" name="materia" id="materia" value="{{$materia}}">
                        <input type="hidden" name="grupo" id="grupo" value="{{$grupo}}">
                        <input type="hidden" name="control" id="control" value="{{$control}}">
                        <input type="hidden" name="periodo" id="periodo" value="{{$periodo}}">
                        <button type="submit" class="btn btn-primary">Continuar</button>
                    </form>
                </div>
            <div class="card bg-success text-white">
                <div class="card-body">
                    Una vez realizada la evaluación, no podrá realizar cambios
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
