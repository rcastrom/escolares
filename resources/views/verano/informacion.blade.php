@extends('layouts.verano')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Coordinación Verano</div>
                <div class="card-body">
                    <h4 class="card-title">Materia: {{$nmateria->nombre_completo_materia}} Grupo: {{$grupo}}</h4>
                    <h5>Clave: {{$materia}}</h5>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Docente</div>
                <div class="card-body">
                    <h4 class="card-title">{{$docente}}</h4>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Horario</div>
                <div class="card-body">
                    <table class="table table-responsive">
                        <thead class="thead-light">
                        <tr>
                            <th>L</th>
                            <th>M</th>
                            <th>M</th>
                            <th>J</th>
                            <th>V</th>
                            <th>S</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>

                                <?php
                                $per=$periodo; $mat=$materia; $gpo=$grupo;
                                for ($i=2;$i<=7;$i++){
                                    $hora=\Illuminate\Support\Facades\DB::table('horarios')
                                        ->select('hora_inicial','hora_final','aula')
                                        ->where('periodo',$periodo)
                                        ->where('materia',$mat)
                                        ->where('grupo',$gpo)
                                        ->where('dia_semana',$i)
                                        ->first();
                                    echo empty($hora->hora_inicial)?
                                        "<td></td>":
                                        "<td>".$hora->hora_inicial."/".$hora->hora_final."<br>(".$hora->aula.")</td>";
                                }
                                ;?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Estudiantes</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-1">
                            #
                        </div>
                        <div class="col-md-2">
                            No de Control
                        </div>
                        <div class="col-md-7">
                            Nombre
                        </div>
                        <div class="col-md-2">
                            Repetidor
                        </div>
                    </div>
                    <?php $i=1; ?>
                    @foreach($alumnos as $alumno)
                        <div class="row">
                            <div class="col-md-1">
                                {{$i}}
                            </div>
                            <div class="col-md-2">
                                {{$alumno->no_de_control}}
                            </div>
                            <div class="col-md-7">
                                {{$alumno->apellido_paterno}} {{$alumno->apellido_materno}} {{$alumno->nombre_alumno}}
                            </div>
                            <div class="col-md-2">
                                {{$alumno->repeticion}}
                            </div>
                        </div>
                        <?php $i++; ?>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Acción a realizar</div>
                <div class="card-body">
                    <form action="{{route('verano_acciones')}}" method="post" role="form" class="form-inline">
                        @csrf
                        <div class="form-group">
                            <label for="accion">Seleccione si desea realizar una acción específica</label>
                            <select name="accion" id="accion" required class="form-control">
                                <option value="" selected>--Seleccione--</option>
                                <option value="1">Dar de alta un estudiante</option>
                                <option value="2">Dar de baja a un estudiante</option>
                                <option value="3">Asignar docente</option>
                                <option value="4">Modificar horario</option>
                                <option value="5">Eliminar grupo</option>
                            </select>
                        </div>
                        <input type="hidden" name="materia" value="{{$materia}}">
                        <input type="hidden" name="grupo" value="{{$grupo}}">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Continuar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-info text-white">
                <div class="card-header">AVISO</div>
                <div class="card-body">
                    <ul>
                        <li>No podrá modificar horario si el grupo tiene alumnos inscritos</li>
                        <li>A una materia paralela no se le puede modificar su horario ni asignar docente.
                            Solo puede cambiar su capacidad</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
