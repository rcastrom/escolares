@extends('layouts.division')

@section('content')
<div class="container">
    <br><br>
    <h3>{{$nperiodo->identificacion_corta}}</h3>
    <h4>Ocupación de aula {{$aula}}</h4>
    <div class="row justify-content-center">
        <div class="col-sm-12 col-md-4">
            <div class="card">
                <div class="card-header">Lunes</div>
                <div class="card-body">
                    @foreach($lunes as $monday)
                        @if(\Illuminate\Support\Facades\DB::table('grupos')->where('periodo',$periodo)->where('materia',$monday->materia)->where('grupo',$monday->grupo)->whereNull('paralelo_de')->count()>0)
                            <div class="row">
                                <div class="col-md-4">
                                    {{substr($monday->hora_inicial,0,5)."-".substr($monday->hora_final,0,5)}}
                                </div>
                                <div class="col-md-4">
                                    <span style="font-size: small;">{{$monday->nombre_abreviado_materia}}</span>
                                </div>
                                <div class="col-md-4">
                                    <span style="font-size: small;">
                                        @if(!empty($monday->rfc))
                                            <?php $nombre=\Illuminate\Support\Facades\DB::table('personal')->where('rfc',$monday->rfc)->first(); ?>
                                            {{$nombre->apellidos_empleado." ".$nombre->nombre_empleado}}
                                        @else
                                            PENDIENTE POR ASIGNAR
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-4">
            <div class="card">
                <div class="card-header">Martes</div>
                <div class="card-body">
                    @foreach($martes as $tuesday)
                        @if(\Illuminate\Support\Facades\DB::table('grupos')->where('periodo',$periodo)->where('materia',$tuesday->materia)->where('grupo',$tuesday->grupo)->whereNull('paralelo_de')->count()>0)
                            <div class="row">
                                <div class="col-md-4">
                                    {{substr($tuesday->hora_inicial,0,5)."-".substr($tuesday->hora_final,0,5)}}
                                </div>
                                <div class="col-md-4">
                                    <span style="font-size: small;">{{$tuesday->nombre_abreviado_materia}}</span>
                                </div>
                                <div class="col-md-4">
                                    <span style="font-size: small;">
                                        @if(!empty($tuesday->rfc))
                                            <?php $nombre=\Illuminate\Support\Facades\DB::table('personal')->where('rfc',$tuesday->rfc)->first(); ?>
                                            {{$nombre->apellidos_empleado." ".$nombre->nombre_empleado}}
                                        @else
                                            PENDIENTE POR ASIGNAR
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-4">
            <div class="card">
                <div class="card-header">Miércoles</div>
                <div class="card-body">
                    @foreach($miercoles as $wed)
                        @if(\Illuminate\Support\Facades\DB::table('grupos')->where('periodo',$periodo)->where('materia',$wed->materia)->where('grupo',$wed->grupo)->whereNull('paralelo_de')->count()>0)
                            <div class="row">
                                <div class="col-md-4">
                                    {{substr($wed->hora_inicial,0,5)."-".substr($wed->hora_final,0,5)}}
                                </div>
                                <div class="col-md-4">
                                    <span style="font-size: small;">{{$wed->nombre_abreviado_materia}}</span>
                                </div>
                                <div class="col-md-4">
                                    <span style="font-size: small;">
                                        @if(!empty($wed->rfc))
                                            <?php $nombre=\Illuminate\Support\Facades\DB::table('personal')->where('rfc',$wed->rfc)->first(); ?>
                                            {{$nombre->apellidos_empleado." ".$nombre->nombre_empleado}}
                                        @else
                                            PENDIENTE POR ASIGNAR
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-sm-12 col-md-4">
            <div class="card">
                <div class="card-header">Jueves</div>
                <div class="card-body">
                    @foreach($jueves as $thu)
                        @if(\Illuminate\Support\Facades\DB::table('grupos')->where('periodo',$periodo)->where('materia',$thu->materia)->where('grupo',$thu->grupo)->whereNull('paralelo_de')->count()>0)
                            <div class="row">
                                <div class="col-md-4">
                                    {{substr($thu->hora_inicial,0,5)."-".substr($thu->hora_final,0,5)}}
                                </div>
                                <div class="col-md-4">
                                    <span style="font-size: small;">{{$thu->nombre_abreviado_materia}}</span>
                                </div>
                                <div class="col-md-4">
                                    <span style="font-size: small;">
                                        @if(!empty($thu->rfc))
                                            <?php $nombre=\Illuminate\Support\Facades\DB::table('personal')->where('rfc',$thu->rfc)->first(); ?>
                                            {{$nombre->apellidos_empleado." ".$nombre->nombre_empleado}}
                                        @else
                                            PENDIENTE POR ASIGNAR
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-4">
            <div class="card">
                <div class="card-header">Viernes</div>
                <div class="card-body">
                    @foreach($viernes as $fri)
                        @if(\Illuminate\Support\Facades\DB::table('grupos')->where('periodo',$periodo)->where('materia',$fri->materia)->where('grupo',$fri->grupo)->whereNull('paralelo_de')->count()>0)
                            <div class="row">
                                <div class="col-md-4">
                                    {{substr($fri->hora_inicial,0,5)."-".substr($fri->hora_final,0,5)}}
                                </div>
                                <div class="col-md-4">
                                    <span style="font-size: small;">{{$fri->nombre_abreviado_materia}}</span>
                                </div>
                                <div class="col-md-4">
                                    <span style="font-size: small;">
                                        @if(!empty($fri->rfc))
                                            <?php $nombre=\Illuminate\Support\Facades\DB::table('personal')->where('rfc',$fri->rfc)->first(); ?>
                                            {{$nombre->apellidos_empleado." ".$nombre->nombre_empleado}}
                                        @else
                                            PENDIENTE POR ASIGNAR
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-4">
            <div class="card">
                <div class="card-header">Sábado</div>
                <div class="card-body">
                    @foreach($sabado as $sat)
                        @if(\Illuminate\Support\Facades\DB::table('grupos')->where('periodo',$periodo)->where('materia',$sat->materia)->where('grupo',$sat->grupo)->whereNull('paralelo_de')->count()>0)
                            <div class="row">
                                <div class="col-md-4">
                                    {{substr($sat->hora_inicial,0,5)."-".substr($sat->hora_final,0,5)}}
                                </div>
                                <div class="col-md-4">
                                    <span style="font-size: small;">{{$sat->nombre_abreviado_materia}}</span>
                                </div>
                                <div class="col-md-4">
                                    <span style="font-size: small;">
                                        @if(!empty($sat->rfc))
                                            <?php $nombre=\Illuminate\Support\Facades\DB::table('personal')->where('rfc',$sat->rfc)->first(); ?>
                                            {{$nombre->apellidos_empleado." ".$nombre->nombre_empleado}}
                                        @else
                                            PENDIENTE POR ASIGNAR
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
