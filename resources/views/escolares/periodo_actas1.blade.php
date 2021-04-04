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
                    <form action="{{route('escolares.actas2')}}" method="post" class="form-inline" role="form">
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
                            <button type="submit" class="btn btn-primary">Continuar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
