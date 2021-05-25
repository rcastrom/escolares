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
                    <form action="{{route('desacad.carreras_ofertar')}}" method="post" role="form">
                        @csrf
                        <table class="table table-active">
                            <thead>
                                <tr>
                                    <th>Carrera</th>
                                    <th>Retícula</th>
                                    <th>¿Se oferta?</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($carreras as $carr)
                                <tr>
                                    <td>{{$carr->nombre_reducido}}</td>
                                    <td>{{$carr->reticula}}</td>
                                    <td><input type="checkbox" class="form-check-input" name="carreras[]" value="{{trim($carr->carrera)."_".trim($carr->reticula)}}"></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <input type="hidden" name="periodo" value="{{$periodo_ficha->fichas}}">
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
