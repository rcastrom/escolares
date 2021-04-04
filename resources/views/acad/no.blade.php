@extends('layouts.academicos')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Jefaturas Académicas</div>
                <div class="card-body">
                    <h2>Error</h2>
                    <p>No fue posible realizar el movimiento solicitado</p>
                    {{$mensaje}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
