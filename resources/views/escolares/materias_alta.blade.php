@extends('layouts.sescolares')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Servicios Escolares</div>
                <div class="card-body">
                    <h4 class="card-title">Materias</h4>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="post" action="{{route('escolares.materias_acciones')}}" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="accion">Acción a realizar</label>
                            <select name="accion" id="accion" required class="form-control">
                                <option value="" selected>--Seleccione--</option>
                                <option value="1">Alta</option>
                                <option value="2">Modificar</option>
                                <option value="3">Vista reticula</option>
                            </select>
                        </div>
                        <div class="form-group ">
                            <label for="carrera">Asociar a la carrera</label>
                            <select name="carrera" id="carrera" required class="form-control">
                                <option value="" selected>--Seleccione--</option>
                                @foreach($carreras as $carr)
                                    <option value="{{$carr->carrera."_".$carr->reticula}}">(Ret {{$carr->reticula}}) {{$carr->nombre_reducido}}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Continuar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
