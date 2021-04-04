@extends('layouts.estudianhambre')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Estudiantes</div>
                <div class="card-body">
                    <h4 class="card-title">Evaluación Docente período {{$nombre_periodo->identificacion_corta}}</h4>
                    <br><br>
                    <form method="post" action="{{route('eval_doc')}}" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="materia">Seleccionar la materia por evaluar</label>
                            <select name="materia" id="materia" class="form-control" required>
                                <option value="" selected>--Seleccione--</option>
                                @foreach($carga as $datos)
                                    <?php
                                        $data=explode("_",$datos);
                                        $cve=$data[0]; $gpo=$data[1];
                                    ?>
                                    <option value="{{$cve."_".$gpo}}">Materia {{$data[2]}}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Continuar</button>
                    </form>
                    <p>La evaluación docente se considerará terminada cuando se terminen de evaluar todas las materias</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
