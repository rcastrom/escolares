@extends('layouts.verano')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Coordinación Verano</div>
                <div class="card-body">
                    <p>En base al listado de carreras - reticulas, seleccione para la creación de los grupos paralelos</p>
                    <form action="{{route('verano_paralelo2')}}" method="post" role="form" class="form-inline">
                        @csrf
                        <div class="form-group">
                            <label for="carrerao">Seleccione la carrera ORIGEN</label>
                            <select name="carrerao" id="carrerao" required class="form-control">
                                <option value="" selected>--ORIGEN--</option>
                                @foreach($carreras as $carrera)
                                    <option value="{{$carrera->carrera.'_'.$carrera->reticula}}">{{$carrera->nombre_reducido.' RET('.$carrera->reticula.')'}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="carrerap">Seleccione la carrera PARALELA</label>
                            <select name="carrerap" id="carrerap" required class="form-control">
                                <option value="" selected>--PARALELA--</option>
                                @foreach($carreras as $carrera)
                                    <option value="{{$carrera->carrera.'_'.$carrera->reticula}}">{{$carrera->nombre_reducido.' RET('.$carrera->reticula.')'}}</option>
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
