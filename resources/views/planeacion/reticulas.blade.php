@extends('layouts.planeacion')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <br>
                    <h4 class="card-title">Carrera {{$ncarrera->nombre_reducido}}</h4>
                    <form method="post" action="{{route('planeacion.vista_reticula')}}" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="espe">Seleccione la vista reticular, en base a la especialidad asignada</label>
                            <select name="espe" id="espe" required class="form-control">
                                <option value="" selected>--Seleccione--</option>
                                @foreach($espe as $especialidades)
                                    <option value="{{$especialidades->especialidad}}">{{$especialidades->nombre_especialidad}}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="carrera" value="{{$carrera}}">
                        <input type="hidden" name="reticula" value="{{$reticula}}">
                        <button type="submit" class="btn btn-primary">Continuar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
