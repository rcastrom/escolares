@extends('layouts.sescolares')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Servicios Escolares</div>
                <div class="card-title">Idioma {{$nidioma->idiomas}} Periodo {{$nperiodo->identificacion_corta}}</div>
                <div class="card-body">
                    <table class="table table-responsive table-light table-striped">
                        <thead>
                            <tr>
                                <th>Curso</th>
                                <th>Inscritos</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $suma=0;?>
                        @foreach($info as $idiomas)
                            @if($idiomas->cantidad>0)
                                <tr>
                                    <td>{{$idiomas->ncurso}}</td>
                                    <td>{{$idiomas->cantidad}}</td>
                                    <?php $suma+=$idiomas->cantidad;?>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                        <tfooter>
                            <tr>
                                <td>Total</td>
                                <td>{{$suma}}</td>
                            </tr>
                        </tfooter>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
