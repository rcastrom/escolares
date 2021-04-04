@extends('layouts.sescolares')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Servicios Escolares</div>
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
                    <form method="post" action="{{ route('escolares.buscar') }}" class="form-inline" role="form">
                        @csrf
                        <legend>Búsqueda de estudiante</legend>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-6">
                                <label for="control"> Dato </label>
                                <input type="text" name="control" id="control" class="form-control"
                                       required maxlength="10" onchange="this.value=this.value.toUpperCase();">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-6">
                                <label for="tbusqueda"> Buscar por: </label>
                                <select name="tbusqueda" id="tbusqueda" class="form-control">
                                    <option value="1" selected>Número de control</option>
                                    <option value="2">Apellido</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Continuar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
