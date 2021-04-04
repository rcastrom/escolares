@extends('layouts.division')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo División Estudios</div>
                <div class="card-body">
                    <h4 class="card-title">Materia: {{$nmateria->nombre_completo_materia}} Grupo: {{$grupo}}</h4>
                    <h5>Clave: {{$materia}}</h5>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Capacidad de grupo</div>
                <div class="card-body">
                    <p>Alta a materia</p>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{route('dep_cap_grupo')}}" method="post" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="capacidad">Modifique la cantidad a estudiantes que podrán seleccionar la materia. Se muestra el valor actual</label>
                            <input type="number" name="capacidad" id="capacidad" required class="form-control" value="{{$cap->capacidad_grupo}}">
                        </div>
                        <input type="hidden" name="materia" id="materia" value="{{$materia}}">
                        <input type="hidden" name="grupo" id="grupo" value="{{$grupo}}">
                        <input type="hidden" name="periodo" id="periodo" value="{{$periodo}}">
                        <input type="hidden" name="cap_old" id="cap_old" value="{{$cap->capacidad_grupo}}">
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
