@extends('layouts.sescolares')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Servicios Escolares</div>
                <div class="card-body">
                    <h4 class="card-title">Actas del período</h4>
                    <br>
                    <p>El módulo se emplea para conocer situaciones de actas (docentes que ya
                    evaluaron, que no han evaluado, entregadas)</p>
                    <form action="{{route('escolares.actas_estatus')}}" method="post" class="form" role="form">
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
                            <label for="accion" class="col-form-label">Estatus de consulta</label>
                            <select name="accion" id="accion" class="form-control">
                                <option value="" selected>--Seleccione--</option>
                                <option value="1">Docentes que no han capturado</option>
                                <option value="2">Docentes que ya capturaron</option>
                                <option value="3">Actas no entregadas a Escolares</option>
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
