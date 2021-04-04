@extends('layouts.division')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Materia ORIGEN</div>
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
                    <form action="{{route('dep_paralelo3')}}" method="post" role="form" class="form-inline">
                        @csrf
                        <div class="form-group">
                            <label for="carrerao">Seleccione la materia ORIGEN</label>
                            <select name="mat_o" id="mat_o" required class="form-control">
                                <option value="" selected>--ORIGEN--</option>
                                @foreach($listado_o as $origen)
                                    <option value="{{$origen->materia."_".$origen->grupo}}">({{$origen->materia}}) {{$origen->nombre_abreviado_materia}} Gpo {{$origen->grupo}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="matp">Seleccione la materia PARALELA</label>
                            <select name="matp" id="matp" required class="form-control">
                                <option value="" selected>--PARALELA--</option>
                                @foreach($listado_p as $destino)
                                    <option value="{{$destino->materia}}">({{$destino->materia}}) {{$destino->nombre_abreviado_materia}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="gpo_p">Grupo Paralelo</label>
                            <input type="text" name="gpo_p" id="gpo_p" required class="form-control" maxlength="3" onchange="this.value=this.vslue.toUpperCase();">
                        </div>
                        <div class="form-group">
                            <label for="cap_n">Capacidad del grupo paralelo</label>
                            <input type="number" name="cap_n" id="cap_n" required class="form-control">
                        </div>
                        <div class="row">
                            <input type="hidden" name="carrera_o" id="carrera_o" value="{{$carrera_o}}">
                            <input type="hidden" name="ret_o" id="ret_o" value="{{$ret_o}}">
                            <input type="hidden" name="carrera_p" id="carrera_p" value="{{$carrera_p}}">
                            <input type="hidden" name="ret_p" id="ret_p" value="{{$ret_p}}">
                            <input type="hidden" name="periodo" id="periodo" value="{{$periodo}}">
                            <button type="submit" class="btn btn-primary">Continuar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
