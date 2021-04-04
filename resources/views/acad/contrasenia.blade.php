@extends('layouts.academicos')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Jefaturas Académicas</div>
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
                    <form action="{{route('acad_contra')}}" method="post" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="contra">Nueva contraseña</label>
                            <input type="password" class="form-control" required name="contra" id="contra">
                        </div>
                        <div class="form-group">
                            <label for="verifica">Confirmar contraseña</label>
                            <input type="password" class="form-control" required name="verifica" id="verifica">
                        </div>
                        <button type="submit" class="btn btn-primary">Continuar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
