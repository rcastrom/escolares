@extends('layouts.planeacion')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md">
                            <h5 class="card-header">Período {{$nperiodo->identificacion_corta}}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md">
                            <h4 class="card-title">Población total carrera {{$ncarrera->nombre_reducido}} ret {{$reticula}}</h4>
                        </div>
                    </div>
                    <table class="table table-responsive">
                        <thead class="thead-light">
                        <tr>
                            <th>Semestre</th>
                            <th>H</th>
                            <th>M</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $suma_h=0; $suma_m=0; ?>
                        @foreach($semestres as $key=>$value)
                            <tr>
                                <td>{{$value}}</td>
                                <td>{{$hombres[$key]}}</td>
                                <?php $suma_h+=$hombres[$key];?>
                                <td>{{$mujeres[$key]}}</td>
                                <?php $suma_m+=$mujeres[$key];?>
                                <td>{{$hombres[$key]+$mujeres[$key]}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Total</td>
                                <td>{{$suma_h}}</td>
                                <td>{{$suma_m}}</td>
                                <td>{{$suma_h+$suma_m}}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
