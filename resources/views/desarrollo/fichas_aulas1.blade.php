@extends('layouts.desarrollo')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Desarrollo Académico</div>
                <div class="card-title"> Período para entrega de fichas: {{$nperiodo->identificacion_corta}}</div>
                <div class="card-body">
                    <p>Del siguiente listado, seleccione las carreras (retícula) que serán ofertadas para
                        el semestre señalado.
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
                    <form action="{{route('desacad.aulas_actualizar')}}" method="post" role="form">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="carrera">Carrera</label>
                                <select name="carrera" id="carrera" required class="form-control">
                                    <option value="" selected>--Seleccione--</option>
                                    @foreach($carreras as $carr)
                                        <option value="{{$carr->carrera}}">{{$carr->nombre_reducido}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="salon">Aula</label>
                                <select name="salon" id="salon" required class="form-control">
                                    <option value="" selected>--Seleccione--</option>
                                    @foreach($aulas as $salon)
                                        <option value="{{$salon->aula}}">{{$salon->aula}} Cap: {{$salon->capacidad}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="cupo">Cupo</label>
                                <input type="number" class="form-control" name="cupo" id="cupo" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Continuar</button>
                        </div>
                    </form>
                    @if($bandera==1)
                        <div class="row">
                            <table class="table table-responsive">
                                <thead>
                                <tr>
                                    <th>Carrera</th>
                                    <th>Aula</th>
                                    <th>Cupo</th>
                                    <th>Disponibles</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($registros as $data)
                                    <tr>
                                        <td>{{$data->nombre_reducido}}</td>
                                        <td>{{$data->aula}}</td>
                                        <td>{{$data->capacidad}}</td>
                                        <td>{{$data->disponibles}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
