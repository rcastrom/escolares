@extends('layouts.sescolares')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Servicios Escolares</div>
                <div class="card-body">
                    <h4 class="card-title">Registro de actas del período {{$nperiodo->identificacion_corta}}</h4>
                    <br>
                    <h5 class="card-title">Docente {{$ndocente->apellidos_empleado}} {{$ndocente->nombre_empleado}}</h5>
                    <br>
                    <form action="{{route('escolares.registro4')}}" method="post" role="form">
                        @csrf
                        @foreach($grupos as $grupo)
                            <div class="form-group">
                                <label for="{{$grupo->materia."_".$grupo->grupo}}">
                                    {{$grupo->nombre_abreviado_materia}}/ Gpo {{$grupo->grupo}}
                                </label>
                                <select name="{{$grupo->materia."_".$grupo->grupo}}" id="{{$grupo->materia."_".$grupo->grupo}}"
                                        required class="form-control">
                                    <?php $entregado=$grupo->entrego;?>
                                    @if($entregado==0)
                                        <option value="0" selected>--Sin entregar--</option>
                                        <option value="1">Entregada</option>
                                    @else
                                         <option value="0">--Sin entregar--</option>
                                         <option value="1" selected>Entregada</option>
                                    @endif
                                </select>
                            </div>
                        @endforeach
                        <input type="hidden" name="periodo" value="{{$periodo}}">
                        <input type="hidden" name="docente" value="{{$docente}}">
                        <button type="submit" class="btn btn-primary">Continuar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
