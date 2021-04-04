@extends('layouts.sescolares')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @foreach($materias as $mater)
                    <?php
                        $semestre_reticula = $mater->semestre_reticula;
                        $renglon = $mater->renglon;
                        $array_reticula[$renglon][$semestre_reticula]['clave'] = $mater->materia;
                        $array_reticula[$renglon][$semestre_reticula]['materia'] = $mater->nombre_abreviado_materia;
                        $array_reticula[$renglon][$semestre_reticula]['creditos_materia'] = $mater->creditos_materia;
                        $array_reticula[$renglon][$semestre_reticula]['horas_teoricas'] = $mater->horas_teoricas;
                        $array_reticula[$renglon][$semestre_reticula]['horas_practicas'] = $mater->horas_practicas;
                    ?>
                    @endforeach
                        <br>
                    <h4 class="card-title">{{$ncarrera->nombre_reducido}} Ret {{$reticula}}</h4>
                    <table align="center" border="1" bordercolor="#000000">
                        <tr>
                            @for($i=1; $i<=10; $i++)
                                <th class="medium_center">Semestre<br>{{$i}}</th>
                            @endfor
                        </tr>
                        @for($renglon=1; $renglon<=8; $renglon++)
                            <tr>
                            @for($semestre=1; $semestre<=10; $semestre++)
                                <?php
                                    if(isset($array_reticula[$renglon][$semestre])){
                                        $materia = $array_reticula[$renglon][$semestre]['materia'];
                                        $clave = $array_reticula[$renglon][$semestre]['clave'];
                                        $horas_teoricas = $array_reticula[$renglon][$semestre]['horas_teoricas'];
                                        $horas_practicas = $array_reticula[$renglon][$semestre]['horas_practicas'];
                                        $creditos_materia = $array_reticula[$renglon][$semestre]['creditos_materia'];
                                        $bandera=1;
                                    }else{
                                        if(\Illuminate\Support\Facades\DB::table('materias_carreras')
                                            ->where('carrera',$carrera)->where('reticula',$reticula)
                                            ->where('semestre_reticula',$semestre)->where('renglon',$renglon)
                                            ->whereNotNull('materia')
                                            ->whereNotNull('especialidad')->count()>0){
                                            $bandera=2;
                                        }else{
                                            $bandera=0;
                                        }
                                    }
                                ?>
                                @if($bandera==1)
                                    <td align="center" height="80" width="90" class="small_center azul">
                                        {{$materia}}<br>{{$clave}}<br>
                                        {{$horas_teoricas}}-{{$horas_practicas}}-{{$creditos_materia}}
                                    </td>
                                @elseif($bandera==2)
                                     <td align="center" height="80" width="90" class="small_center naranja">Esp</td>
                                @else
                                     <td align="center" height="80" width="90" class="small_center"></td>
                                @endif
                            @endfor
                            </tr>
                        @endfor
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Materia Nueva</h4>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="post" action="{{route('escolares.materia_nueva')}}" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="cve">Clave interna para la materia</label>
                            <input type="text" class="form-control" name="cve" id="cve" required maxlength="7" onchange="this.value=this.value.toUpperCase();">
                        </div>
                        <div class="form-group">
                            <label for="nivel">Nivel escolar</label>
                            <select name="nivel" id="nivel" required class="form-control">
                                <option value="" selected>--Seleccione--</option>
                                <option value="L">Licenciatura</option>
                                <option value="P">Posgrado</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tipo_materia">Tipo de materia</label>
                            <select name='tipo_materia' id="tipo_materia" class="form-control" required>
                                <option value="" selected>--Seleccione--</option>
                                <option value='1'>Materia Curricular Base</option>
                                <option value='2'>Materia Curricular Optativa</option>
                                <option value='3'>Materia Curricular de Especialidad</option>
                                <option value='4'>Materia Extra-Curricular</option>
                                <option value='5'>Materia Curricular Acreditacion Especial</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="area">Área académica</label>
                            <select name='area' id="area" class="form-control" required>
                                <option value="" selected>--Seleccione--</option>
                                @foreach($acad as $academico)
                                    <option value="{{$academico->clave_area}}">{{$academico->descripcion_area}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cve_of">Clave oficial para la materia</label>
                            <input type="text" class="form-control" name="cve_of" id="cve_of" required maxlength="7" onchange="this.value=this.value.toUpperCase();">
                        </div>
                        <div class="form-group">
                            <label for="nombre_completo">Nombre completo</label>
                            <input type="text" class="form-control" name="nombre_completo" id="nombre_completo" required onchange="this.value=this.value.toUpperCase();">
                        </div>
                        <div class="form-group">
                            <label for="nombre_abrev">Nombre abreviado</label>
                            <input type="text" class="form-control" name="nombre_abrev" id="nombre_abrev" required onchange="this.value=this.value.toUpperCase();">
                        </div>
                        <div class="form-group">
                            <label for="horas_teoricas">Horas teóricas</label>
                            <input type="number" class="form-control" name="horas_teoricas" id="horas_teoricas" required>
                        </div>
                        <div class="form-group">
                            <label for="horas_practicas">Horas prácticas</label>
                            <input type="number" class="form-control" name="horas_practicas" id="horas_practicas" required>
                        </div>
                        <div class="form-group">
                            <label for="creditos">Créditos totales</label>
                            <input type="number" class="form-control" name="creditos" id="creditos" required>
                        </div>
                        <div class="form-group">
                            <label for="certificado">Órden de la materia en el certificado</label>
                            <input type="number" class="form-control" name="certificado" id="certificado" required>
                        </div>
                        <div class="form-group">
                            <label for="especialidad">Indique si pertenece a alguna especialidad</label>
                            <select name='especialidad' id="especialidad" class="form-control" required>
                                <option value="" selected>--Seleccione--</option>
                                <option value="0">No pertenece a ninguna especialidad</option>
                                @foreach($espe as $especialidad)
                                    <option value="{{$especialidad->especialidad}}">{{$especialidad->nombre_especialidad}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="semestre">Semestre de asignación</label>
                            <select name='semestre' id="semestre" class="form-control" required>
                                <option value="" selected>--Seleccione--</option>
                                @for($i=1;$i<=9;$i++)
                                    <option value="{{$i}}">{{$i}}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="renglon">Renglón de asignación</label>
                            <select name='renglon' id="renglon" class="form-control" required>
                                <option value="" selected>--Seleccione--</option>
                                @for($i=1;$i<=10;$i++)
                                    <option value="{{$i}}">{{$i}}</option>
                                @endfor
                            </select>
                        </div>
                        <input type="hidden" name="carrera" value="{{$carrera}}">
                        <input type="hidden" name="reticula" value="{{$reticula}}">
                        <button type="submit" class="btn btn-primary">Continuar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
