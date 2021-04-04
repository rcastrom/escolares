@extends('layouts.planeacion')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Módulo Planeación</div>
                <div class="card-body">
                    <h3>Edades de alumnos x carrera (911) Período {{$nperiodo->identificacion_corta}}</h3>
                    <h4>Carrera {{$ncarrera->nombre_reducido}} retícula {{$reticula}}</h4>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <td></td>
                            <th colspan='20' align="center">Grado de avance (semestre)</th>
                        </tr>
                        <tr>
                            <td></td>
                            @for($i=1;$i<=10;$i++)
                                @if($i<=9)
                                    <td align="center" colspan="2">{{$i}}</td>
                                @else
                                    <td colspan="2">>9</td>
                                @endif
                            @endfor
                        </tr>
                        <tr>
                            <td>Edad</td>
                            @for($i=1;$i<=20;$i++)
                                @if($i%2==0)
                                    <td>M</td>
                                @else
                                    <td>H</td>
                                @endif
                            @endfor
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($edades as $key=>$value)
                            <tr>
                                <td>{{$value}} años</td>
                                @for($i=1;$i<=20;$i++)
                                    @if($i%2==0)
                                        <td>{{$mujeres[$key][$i]}}</td>
                                    @else
                                        <td>{{$hombres[$key][$i]}}</td>
                                    @endif
                                @endfor
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
