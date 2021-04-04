@extends('layouts.planeacion')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Planeación</div>
                <div class="card-body">
                    <form action="{{route('planeacion.egreso')}}" method="post" class="form" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="periodo1" class="col-form-label">Período que ingresaron</label>
                            <select name="periodo1" id="periodo1" class="form-control">
                                <option value="1" selected>--Indistinto--</option>
                                @foreach($periodos as $per)
                                    <option value="{{$per->periodo}}">{{$per->identificacion_corta}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="periodo2" class="col-form-label">Período de corte</label>
                            <select name="periodo2" id="periodo2" class="form-control">
                                @foreach($periodos as $per)
                                    <option value="{{$per->periodo}}"{{$per->periodo==$periodo?' selected':''}}>{{$per->identificacion_corta}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="estudio" class="col-form-label">Carrera</label>
                            <select name="estudio" id="estudio" class="form-control">
                                <option value="1" selected>--Indistinto--</option>
                                @foreach($carreras as $car)
                                    <option value="{{$car->carrera."_".$car->reticula}}">Ret({{$car->reticula}}){{$car->nombre_reducido}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tipo" class="col-form-label">Tipo de búsqueda</label>
                            <select name="tipo" id="tipo" class="form-control" required>
                                <option value="" selected>--Seleccione--</option>
                                <option value="E">Egresados</option>
                                <option value="T">Titulados</option>
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
