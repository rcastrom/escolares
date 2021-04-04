@extends('layouts.estudianhambre')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Módulo Estudiantes</div>
                <div class="card-body">
                    <h4 class="card-title">{{ $alumno->apellido_paterno.' '.$alumno->apellido_materno.' '.$alumno->nombre_alumno }}</h4>
                        <p class="card-text">Número de control: {{ $alumno->no_de_control }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Selección grupo  materia {{ $nmateria->nombre_abreviado_materia }}</div>
                <div class="card-body">
                    <form action="{{route('alumno.seleccion')}}" method="post" role="form">
                        @csrf
                   <table class="table table-responsive">
                       <thead class="thead-light">
                       <tr>
                           <th>Grupo</th>
                           <th>Global</th>
                           <th>Estatus</th>
                           <th>Docente</th>
                           <th>Lunes</th>
                           <th>Martes</th>
                           <th>Miércoles</th>
                           <th>Jueves</th>
                           <th>Viernes</th>
                           <th>Sábado</th>
                       </tr>
                       </thead>
                       <tbody>
                           @foreach($info_grupos as $value)
                               <tr>
                                   <td>{{$value->grupo}}</td>
                                   <td>
                                       <select name="{{'op_'.$materia.'_'.$value->grupo}}" id="{{'op_'.$materia.'_'.$value->grupo}}">
                                           <option value="S">Si</option>
                                           <option value="N" selected>No</option>
                                       </select>
                                   </td>
                                   <td>
                                       @if($value->capacidad_grupo>0)
                                           <input type="radio" name="materia" value="{{$materia.'_'.$value->grupo}}">Cursar
                                       @else
                                           Cerrado
                                       @endif
                                   </td>
                                   <td>
                                       @if($value->rfc=='' || $value->rfc == null)
                                           Sin profesor asignado
                                       @else
                                           <?php
                                           $rfc=$value->rfc;  $doc=\Illuminate\Support\Facades\DB::table('personal')
                                               ->select('apellidos_empleado','nombre_empleado')->where('rfc',$rfc)->first();
                                           echo $doc->apellidos_empleado.' '.$doc->nombre_empleado;
                                           ?>
                                       @endif
                                   </td>
                                   @for($i=2;$i<=7;$i++)
                                       <td>
                                           <?php
                                           $gpo=$value->grupo;
                                            $hora=\Illuminate\Support\Facades\DB::table('horarios')->where('periodo',$periodo)
                                           ->where('materia',$materia)->where('grupo',$gpo)->where('dia_semana',$i)->first();
                                            if(!empty($hora)){
                                                $horario=$hora->hora_inicial."-".$hora->hora_final."/".$hora->aula;
                                            }else{
                                                $horario='';
                                            }
                                           ?>
                                           {{$horario}}
                                       </td>
                                   @endfor
                               </tr>
                           @endforeach
                       </tbody>
                   </table>
                        <input type="hidden" name="periodo" value="{{$periodo}}">
                        <input type="hidden" name="repeticion" value="{{$repeticion}}">
                        <button type="submit" class="btn btn-primary">Continuar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
