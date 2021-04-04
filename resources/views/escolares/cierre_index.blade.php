@extends('layouts.sescolares')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Servicios Escolares</div>
                <div class="card-body">
                    <h4 class="card-title">Cierre del semestre</h4>
                    <br>
                    <div class="card bg-danger text-white">
                        <div class="card-body">MUY IMPORTANTE</div>
                        <p>El orden de los pasos a seguir <strong><u>debe</u></strong> ser el siguiente:
                        <ol>
                            <li>Actualizar kárdex</li>
                            <li>Cálculo de promedios</li>
                            <li>Actualización de estatus (NO EN VERANO)</li>
                            <li>Actualización de semestre (NO EN VERANO)</li>
                            <li>Actualización de curso especial</li>
                            <li>Actualización de actividades complementarias (*)</li>
                        </ol>
                        (*) Si no existen calificaciones de actividades complementarias registradas, omita el paso.
                        <p>NO MODIFIQUE EL ORDEN DE LOS PASOS</p>
                    </div>
                    <form action="{{route('escolares.cierre_semestre')}}" method="post" class="form" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="periodo">Período</label>
                            <select name="periodo" id="periodo" class="form-control">
                                @foreach($periodos as $per)
                                    <option value="{{$per->periodo}}"{{$per->periodo==$periodo?' selected':''}}>{{$per->identificacion_corta}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="orden">Acción a realizar</label>
                            <select name="orden" id="orden" class="form-control" required>
                                <option value="" selected>--Seleccione</option>
                                <option value="1">1.- Actualizar kárdex</option>
                                <option value="2">2.- Cálculo de promedios</option>
                                <option value="3">3.- Actualización de estatus (NO EN VERANO)</option>
                                <option value="4">4.- Actualización de semestre (NO EN VERANO)</option>
                                <option value="5">5.- Actualización de curso especial</option>
                                <option value="6">6.- Actualización de actividades complementarias</option>
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
