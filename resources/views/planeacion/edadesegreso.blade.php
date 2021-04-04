@extends('layouts.planeacion')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Módulo Planeación</div>
                <div class="card-title">Tipo de búsqueda: {{$tconsulta}}</div>
                <div class="card-title">Carrera: {{$ncarrera}}</div>
                <div class="card-title">Período de finalización: {{$nperiodo_fin->identificacion_corta}}</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <td>Edad</td>
                            <td>M</td>
                            <td>H</td>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($edades as $key=>$value)
                            <tr>
                                <td>{{$value}} años</td>
                                <td>{{$mujeres[$key]}}</td>
                                <td>{{$hombres[$key]}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
