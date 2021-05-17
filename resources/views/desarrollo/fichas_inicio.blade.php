@extends('layouts.desarrollo')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Desarrollo Académico</div>
                <div class="card-body">
                    <p>Indique los valores iniciales para la entrega de fichas
                    </p>
                    <form action="{{route('desacad.parametros_fichas')}}" method="post" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="per_ficha">Semestre al que ingresarán los aspirantes</label>
                            <select name="per_ficha" id="per_ficha" required class="form-control">
                                @foreach($periodos as $periodo)
                                    <option value="{{$periodo->periodo}}" {{$periodo->periodo==$periodo_ficha->fichas?" selected":""}}>{{$periodo->identificacion_larga}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="entrega">Inicio de entrega de fichas</label>
                            <input type="date" class="form-control" name="entrega" id="entrega" value="{{$periodo_ficha->entrega}}">
                        </div>
                        <div class="form-group">
                            <label for="termina">Fin de entrega de fichas</label>
                            <input type="date" class="form-control" name="termina" id="termina" value="{{$periodo_ficha->termina}}">
                        </div>
                        <div class="form-group">
                            <label for="inicio_prope">Inicio de curso propedéutico</label>
                            <input type="date" class="form-control" name="inicio_prope" id="inicio_prope" value="{{$periodo_ficha->entrega}}">
                        </div>
                        <div class="form-group">
                            <label for="fin_prope">Fin de curso propedéutico</label>
                            <input type="date" class="form-control" name="fin_prope" id="fin_prope" value="{{$periodo_ficha->termina}}">
                        </div>
                        <input type="hidden" name="periodo" value="{{$periodo_ficha->fichas}}">
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
