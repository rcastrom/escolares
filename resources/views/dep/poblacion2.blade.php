@extends('layouts.division')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <br>
                    <h3 class="card-title">Período {{$nperiodo->identificacion_corta}}</h3>
                    <h4 class="card-header">Población total carrera {{$ncarrera->nombre_reducido}} ret {{$reticula}}</h4>
                    <table class="table table-responsive">
                        <thead class="thead-light">
                        <tr>
                            <th>Semestre</th>
                            <th>Población</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $suma=0; ?>
                        @foreach($cantidad as $poblacion)
                            <tr>
                                <td>{{$poblacion->semestre}}</td>
                                <td>{{$poblacion->inscritos}}</td>
                                <?php $suma+=$poblacion->inscritos; ?>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Total</td>
                                <td>{{$suma}}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-header">Población masculina</h4>
                    <table class="table table-responsive">
                        <thead class="thead-light">
                        <tr>
                            <th>Semestre</th>
                            <th>Población</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sum_h=0; ?>
                        @foreach($pob_masc as $hombres)
                            <tr>
                                <td>{{$hombres->semestre}}</td>
                                <td>{{$hombres->inscritos}}</td>
                                <?php $sum_h+=$hombres->inscritos; ?>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td>Total</td>
                            <td>{{$sum_h}}</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-header">Población Femenina</h4>
                    <table class="table table-responsive">
                        <thead class="thead-light">
                        <tr>
                            <th>Semestre</th>
                            <th>Población</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $suma_m=0; ?>
                        @foreach($pob_fem as $mujeres)
                            <tr>
                                <td>{{$mujeres->semestre}}</td>
                                <td>{{$mujeres->inscritos}}</td>
                                <?php $suma_m+=$mujeres->inscritos; ?>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td>Total</td>
                            <td>{{$suma_m}}</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
