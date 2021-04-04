@extends('layouts.planeacion')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Módulo Planeación</div>
                <div class="card-title">Tipo de búsqueda: {{$tconsulta}}</div>
                <div class="card-title">Período de finalización: {{$nperiodo_fin->identificacion_corta}}</div>
                <div class="card-body">
                    <p>Población Masculina</p>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <td>Carrera</td>
                            <td>Total</td>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data_h as $key=>$value)
                            <tr>
                                <td>{{$value->nombre_carrera}}</td>
                                <td>{{$value->egresados}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-body">
                    <p>Población Femenina</p>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <td>Carrera</td>
                            <td>Total</td>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data_m as $key=>$value)
                            <tr>
                                <td>{{$value->nombre_carrera}}</td>
                                <td>{{$value->egresados}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-body">
                    <p>Total</p>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <td>Carrera</td>
                            <td>Total</td>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data_t as $key=>$value)
                            <tr>
                                <td>{{$value->nombre_carrera}}</td>
                                <td>{{$value->egresados}}</td>
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
