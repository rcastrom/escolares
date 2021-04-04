@extends('layouts.academicos')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Jefaturas Académicas</div>
                <div class="card-body">
                    <div class="card" style="width: 22rem;">
                        <div class="card-body">
                            <h5 class="card-title">Cambio de contraseña para docente</h5>
                            <h6 class="card-subtitle mb-2 text-muted">
                                {{$info->apellidos_empleado}} {{$info->nombre_empleado}}</h6>
                            <p class="card-text">Usuario del sistema: {{$info->correo_institucion}}</p>
                        </div>
                    </div>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{route('acad_cambiar_contra_doc2')}}" method="post" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="contra">Favor de indicar la nueva contraseña</label>
                            <input type="password" name="contra" id="contra" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="ccontra">Confirmar la nueva contraseña</label>
                            <input type="password" name="ccontra" id="ccontra" class="form-control" required>
                        </div>
                        <input type="hidden" name="rfc" value="{{$info->rfc}}">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Continuar</button>
                        </div>
                    </form>
                    <p>El sistema distingue entre el uso de mayúsculas y minúsculas</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
