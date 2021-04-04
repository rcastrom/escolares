@extends('layouts.estudianhambre')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Estudiantes</div>
                <div class="card-body">
                    <h4 class="card-title">Evaluación docente del período {{$nperiodo->identificacion_corta}}</h4>
                    <br>
                    <h5>Docente: {{$ndoc}}</h5>
                    <h6>Materia: {{$nmat->nombre_abreviado_materia}}</h6>
                    <br><br>
                    <div class="row">
                        <div class="col-md-12">
                            Para cada una de las preguntas que se te presentan a continuación, evalúa en la escala
                            del 1 al 5 de acuerdo a lo siguiente:
                            <table class="table table-responsive">
                                <tr>
                                    <td>1.- Altamente en desacuerdo</td>
                                    <td>2.- En desacuerdo</td>
                                    <td>3.- Indiferente</td>
                                    <td>4.- De acuerdo</td>
                                    <td>5.- Totalmente de acuerdo</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <form method="post" action="{{route('eval_docente')}}" role="form">
                        @csrf
                        @foreach($preguntas as $pregunta)
                            <div class="form-group">
                                <label for="{{$pregunta->no_pregunta}}">{{$pregunta->pregunta}}</label>
                                <select name="{{$pregunta->no_pregunta}}" id="{{$pregunta->no_pregunta}}" required class="form-control">
                                    <option value="" selected>--Seleccione--</option>
                                    <option value="1">Altamente en desacuerdo</option>
                                    <option value="2">En desacuerdo</option>
                                    <option value="3">Indiferente</option>
                                    <option value="4">De acuerdo</option>
                                    <option value="5">Totalmente de acuerdo</option>
                                </select>
                            </div>
                        @endforeach
                        <input type="hidden" name="materia" value="{{$mat}}">
                        <input type="hidden" name="gpo" value="{{$gpo}}">
                        <input type="hidden" name="rfc" value="{{$rfc}}">
                        <button type="submit" class="btn btn-primary">Continuar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
