@extends('layouts.docentes')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Personal Docente</div>
                <div class="card-body">
                    <h3>Evaluación residencias profesionales</h3>
                    <table class="table table-responsive">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Control</th>
                            <th>Nombre</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i=1;?>
                        @foreach($quienes as $quien)
                            <tr>
                                <td>{{$i}}</td>
                                <td>{{$quien->no_de_control}}</td>
                                <td>{{$quien->apellido_paterno}} {{$quien->apellido_materno}} {{$quien->nombre_alumno}}</td>
                                <?php
                                    if(is_null($quien->calificacion)){ ?>
                                       <td><i class="fas fa-sort-numeric-up"></i>
                                           <a href="/personal/residencias/evaluar/{{base64_encode($per_residencias)}}/{{base64_encode($quien->materia)}}/{{base64_encode($quien->grupo)}}/{{base64_encode($quien->no_de_control)}}" title="Calificaciones">Evaluar</a></td>
                                <?php }else{ ?>
                                   <td></td>
                                <?php } ?>
                                <td><i class="fas fa-print"></i>
                                    <a href="/personal/residencias/acta/{{$per_residencias}}/{{$quien->materia}}/{{$quien->grupo}}" title="Acta">Imprimir acta final</a></td>
                            </tr>
                            <?php $i++;?>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card bg-success text-white">
                <div class="card-body">
                    Una vez realizada la evaluación, no podrá realizar cambios
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
