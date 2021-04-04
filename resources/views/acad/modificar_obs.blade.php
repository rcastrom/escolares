@extends('layouts.academicos')

@section('content')
<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Observaciones para el horario</h4>
                    <div class="row">
                        <div class="col-sm-8 col-md-8">
                            {{$obs->observaciones}}
                        </div>
                        <div class="col-sm-2 col-md-2">
                            <i class="fas fa-wrench"></i>
                            <a href="/acad/modificar/obs/{{$periodo}}/{{$rfc}}" title="Modificar">
                                Modificar</a>
                        </div>
                        <div class="col-sm-2 col-md-2">
                            <i class="fas fa-trash-alt"></i>
                            <a href="/acad/eliminar/obs/{{$periodo}}/{{$rfc}}" title="Eliminar">
                                Eliminar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
