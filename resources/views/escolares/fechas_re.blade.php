@extends('layouts.sescolares')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Servicios Escolares</div>
                <h4 class="card-title">Establecer fechas para reinscripción</h4>
                <h5 class="card-title">Período: {{$nperiodo->identificacion_corta}}</h5>
                <h5 class="card-title">Carrera: {{$ncarrera->nombre_reducido}}</h5>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{route('escolares.fechas-reinscripcion')}}" method="post" role="form">
                        @csrf
                        <div class="form-group row">
                            <label for="dia" class="col-sm-4 col-form-label">Día</label>
                            <div class="col-sm-8">
                                <input type="date" name="dia" id="dia" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="horaini" class="col-sm-4 col-form-label">Hora inicio</label>
                            <div class="col-sm-8">
                                <input type="time" name="horaini" id="horaini" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="horafin" class="col-sm-4 col-form-label">Hora final</label>
                            <div class="col-sm-8">
                                <input type="time" name="horafin" id="horafin" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="intervalo" class="col-sm-4 col-form-label">Intervalo</label>
                            <div class="col-sm-8">
                                <select name="intervalo" class="form-control" required>
                                    <option value="20">CADA 20 MINUTOS</option>
                                    <option value="30">CADA 30 MINUTOS</option>
                                    <option value="40" selected>CADA 40 MINUTOS</option>
                                    <option value="50">CADA 50 MINUTOS</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="personas" class="col-sm-4 col-form-label">Personas por intervalo</label>
                            <div class="col-sm-8">
                                <select name="personas" class="form-control" required>
                                    <option value="10">10 PERSONAS</option>
                                    <option value="20">20 PERSONAS</option>
                                    <option value="30" selected>30 PERSONAS</option>
                                    <option value="40">40 PERSONAS</option>
                                    <option value="50">50 PERSONAS</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="carrera" value="{{$carrera}}">
                        <input type="hidden" name="periodo" value="{{$periodo}}">
                        <div class="form-group">
                            <div class="col-sm-8">
                                <button type="submit" class="btn btn-primary">Continuar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
