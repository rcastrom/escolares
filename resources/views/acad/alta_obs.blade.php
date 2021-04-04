@extends('layouts.academicos')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Jefaturas Académicas</div>
                <div class="card-body">
                    <h4>Docente: {{$personal->apellidos_empleado}} {{$personal->nombre_empleado}}</h4>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{route('acad_altaobs')}}" method="post" role="form">
                        @csrf
                        <label for="obs">Observación para el horario</label>
                        <textarea name="obs" id="obs" cols="30" rows="10" class="form-control" required
                                  onchange="this.value=this.value.toUpperCase();"></textarea>
                        <button type="submit" class="btn btn-primary">Continuar</button>
                        <input type="hidden" name="periodo" value="{{$periodo}}">
                        <input type="hidden" name="rfc" value="{{$rfc}}">
                    </form>
                </div>
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-md-12">
                                Tratar de generar dos observaciones para el mismo docente en el mismo período generará un mensaje de error
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
