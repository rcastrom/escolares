@extends('layouts.desarrollo')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Desarrollo Académico</div>
                <div class="card-title">Período para entrega de fichas: {{$nperiodo->identificacion_corta}}</div>
                <div class="card-body">
                    <p>Realice las modificaciones que considere convenientes.
                    </p>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{route('desacad.aulas_actualizar2')}}" method="post" role="form">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="carrera">Carrera</label>
                                <select name="carrera" id="carrera" required class="form-control">
                                    <option value="" selected>--Seleccione--</option>
                                    @foreach($carreras as $carr)
                                        <option value="{{$carr->carrera}}" {{$carr->carrera==$datos->carrera?" selected":""}}>{{$carr->nombre_reducido}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="salon">Aula</label>
                                <select name="salon" id="salon" required class="form-control" readonly="true" disabled>
                                    <option value="" selected>--Seleccione--</option>
                                    @foreach($salones as $salon)
                                        <option value="{{$salon->aula}}" {{$salon->aula==$datos->aula?" selected":""}}>{{$salon->aula}} Cap: {{$salon->capacidad}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="cupo">Capacidad</label>
                                <input type="number" class="form-control" name="cupo" id="cupo" required value="{{$datos->capacidad}}">
                            </div>
                        </div>
                        <input type="hidden" name="aula" id="aula" value="{{$aula}}">
                        <input type="hidden" name="disponibles" id="disponibles" value="{{$datos->disponibles}}">
                        <input type="hidden" name="cap_actual" id="cap_actual" value="{{$datos->capacidad}}">
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
