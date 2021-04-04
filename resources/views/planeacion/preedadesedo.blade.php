@extends('layouts.planeacion')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Planeación</div>
                <div class="card-body">
                    <form action="{{route('planeacion.edadesedo')}}" method="post" class="form" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="periodo" class="col-form-label">Período</label>
                            <select name="periodo" id="periodo" class="form-control">
                                @foreach($periodos as $per)
                                    <option value="{{$per->periodo}}"{{$per->periodo==$periodo?' selected':''}}>{{$per->identificacion_corta}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="carrera" class="col-form-label">Carrera</label>
                            <select name="carrera" id="carrera" class="form-control" required>
                                <option value="" selected>--Seleccione--</option>
                                @foreach($carreras as $carr)
                                    <option value="{{$carr->carrera."_".$carr->reticula}}">Ret ({{$carr->reticula}}) {{$carr->nombre_reducido}}</option>
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
