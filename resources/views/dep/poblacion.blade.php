@extends('layouts.division')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo División Estudios</div>
                <div class="card-body">
                    <h4 class="card-title">Período {{$nperiodo->identificacion_corta}}</h4>
                    <table class="table table-responsive">
                        <thead class="thead-light">
                        <tr>
                            <th>Carrera</th>
                            <th>Retícula</th>
                            <th>Población</th>
                            <th>Desglose</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $suma=0; ?>
                        @foreach($inscritos as $cantidad)
                            <tr>
                                <td>{{$cantidad->ncarrera}}</td>
                                <td>{{$cantidad->reticula}}</td>
                                <td>{{$cantidad->cantidad}}</td>
                                <td><i class="far fa-question-circle"></i>
                                    <a href="/dep/estadistica/{{$periodo}}/{{$cantidad->carrera}}/{{$cantidad->reticula}}"
                                       title="Desglosar">Mayor información</a></td>
                                <?php $suma+=$cantidad->cantidad; ?>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">Total</td>
                                <td>{{$suma}}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
