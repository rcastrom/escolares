@extends('layouts.sescolares')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Servicios Escolares</div>
                <div class="card-body">
                    <h4 class="card-title">Impresión de certificado</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Información para el certificado</div>
                <div class="card-body">
                    <form action="{{route('escolares.certificado_pdf')}}" method="post" role="form">
                        @csrf
                        <input type="hidden" name="control" value="{{$info['control']}}">
                        <input type="hidden" name="director" value="{{$info["director"]}}">
                        <input type="hidden" name="registro" value="{{$info["registro"]}}">
                        <input type="hidden" name="libro" value="{{$info["libro"]}}">
                        <input type="hidden" name="foja" value="{{$info["foja"]}}">
                        <input type="hidden" name="fecha_registro" value="{{$info["fregistro"]}}">
                        <input type="hidden" name="fecha_emision" value="{{$info["femision"]}}">
                        <input type="hidden" name="iniciales" value="{{$info["iniciales"]}}">
                        <input type="hidden" name="tipo" value="{{$info["tipo"]}}">
                        <input type="hidden" name="autoridad_educativa" value="{{$info["emite_equivalencia"]}}">
                        <input type="hidden" name="folio" value="{{$info["equivalencia"]}}">
                        <input type="hidden" name="fecha_elaboracion" value="{{$info["fequivalencia"]}}">
                        <button type="submit" class="btn btn-primary">Continuar</button>
                    </form>
                </div>
            </div>
    </div>
</div>
@endsection
