@extends('layouts.division')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo División Estudios</div>
                <div class="card-body">
                    <p>En base al listado de carreras - reticulas, seleccione para crear un grupo</p>
                    <form action="{{route('dep_lista2')}}" method="post" role="form" class="form-inline">
                        @csrf
                        <div class="form-group">
                            <label for="carrera">Seleccione la carrera por buscar</label>
                            <select name="carrera" id="carrera" required class="form-control">
                                <option value="" selected>--Seleccione--</option>
                                @foreach($carreras as $carrera)
                                    <option value="{{$carrera->carrera.'_'.$carrera->reticula}}">{{$carrera->nombre_reducido.' RET('.$carrera->reticula.')'}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="periodo">Periodo de alta</label>
                            <select name="periodo" id="periodo" required class="form-control">
                                @foreach($periodos as $per)
                                    <option value="{{$per->periodo}}"{{$per->periodo==$periodo?' selected':''}}>{{$per->identificacion_corta}}</option>
                                @endforeach
                            </select>
                        </div>
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
