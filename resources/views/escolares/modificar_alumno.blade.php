@extends('layouts.sescolares')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Servicios Escolares</div>
                <h4 class="card-title">Modificar datos alumno</h4>
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
                    <div class="alert-dismissible">Los datos marcados con (*) son obligatorios</div>
                    <form action="{{route('escolares.actualizar_alumno')}}" method="post" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="control" class="col-sm-4 col-form-label">Número de control (*)</label>
                            <div class="col-sm-8">
                                <input type="text" name="control" id="control" class="form-control" value="{{$control}}" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="appat" class="col-sm-4 col-form-label">Apellido Paterno</label>
                            <div class="col-sm-8">
                                <input type="text" name="appat" id="appat" class="form-control" onchange="this.value=this.value.toUpperCase();" value="{{$alumno->apellido_paterno}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="apmat" class="col-sm-4 col-form-label">Apellido Materno (*)</label>
                            <div class="col-sm-8">
                                <input type="text" name="apmat" id="apmat" class="form-control" required onchange="this.value=this.value.toUpperCase();" value="{{$alumno->apellido_materno}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nombre" class="col-sm-4 col-form-label">Nombre (*)</label>
                            <div class="col-sm-8">
                                <input type="text" name="nombre" id="nombre" class="form-control" required onchange="this.value=this.value.toUpperCase();" value="{{$alumno->nombre_alumno}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plan" class="col-sm-4 col-form-label">Plan de estudios (*)</label>
                            <div class="col-sm-8">
                                <select name="plan" id="plan" required class="form-control">
                                    <option value="" selected>--Seleccione--</option>
                                    @foreach($planes as $plan)
                                        <option value="{{$plan->plan_de_estudio}}" {{$plan->plan_de_estudio==$alumno_plan?' selected':''}}>(Plan {{$plan->plan_de_estudio}}) {{$plan->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ingreso" class="col-sm-4 col-form-label">Período de ingreso (*)</label>
                            <div class="col-sm-8">
                                <select name="ingreso" id="ingreso" required class="form-control">
                                    @foreach($periodos as $per)
                                        <option value="{{$per->periodo}}" {{$per->periodo==$periodo_ingreso?' selected':''}}>{{$per->identificacion_larga}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="semestre" class="col-sm-4 col-form-label">Semestre (*)</label>
                            <div class="col-sm-8">
                                <input type="number" name="semestre" id="semestre" class="form-control" required onchange="this.value=this.value.toUpperCase();" value="{{$alumno->semestre}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nss" class="col-sm-4 col-form-label">NSS</label>
                            <div class="col-sm-8">
                                <input type="nss" name="nss" id="nss" class="form-control" onchange="this.value=this.value.toUpperCase();" value="{{$alumno->nss}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="curp" class="col-sm-4 col-form-label">CURP (*)</label>
                            <div class="col-sm-8">
                                <input type="text" name="curp" id="curp" class="form-control" required maxlength="18" onchange="this.value=this.value.toUpperCase();" value="{{$alumno->curp_alumno}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="calle" class="col-sm-4 col-form-label">Calle y número</label>
                            <div class="col-sm-8">
                                <input type="text" name="calle" id="calle" class="form-control" onchange="this.value=this.value.toUpperCase();" value="{{$generales->domicilio_calle}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="colonia" class="col-sm-4 col-form-label">Colonia</label>
                            <div class="col-sm-8">
                                <input type="text" name="colonia" id="colonia" class="form-control" onchange="this.value=this.value.toUpperCase();" value="{{$generales->domicilio_colonia}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cp" class="col-sm-4 col-form-label">CP</label>
                            <div class="col-sm-8">
                                <input type="text" name="cp" id="cp" class="form-control" maxlength="5" onchange="this.value=this.value.toUpperCase();" value="{{$generales->codigo_postal}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="telcel" class="col-sm-4 col-form-label">Teléfono</label>
                            <div class="col-sm-8">
                                <input type="text" name="telcel" id="telcel" class="form-control" onchange="this.value=this.value.toUpperCase();" value="{{$generales->telefono}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="correo" class="col-sm-4 col-form-label">Correo electrónico</label>
                            <div class="col-sm-8">
                                <input type="email" name="correo" id="correo" class="form-control" onchange="this.value=this.value.toLowerCase();" value="{{$alumno->correo_electronico}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="rev" class="col-sm-4 col-form-label">Semestres revalidados</label>
                            <div class="col-sm-8">
                                <input type="number" name="periodos_revalidacion" id="periodos_revalidacion" class="form-control" value="{{$alumno->periodos_revalidacion}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tipo" class="col-sm-4 col-form-label">Tipo de ingreso (*)</label>
                            <div class="col-sm-8">
                                <select name="tipo" id="tipo" required class="form-control">
                                    @foreach($tipos_ingreso as $tingreso)
                                        <option value="{{$tingreso->id}}" {{$tingreso->id==$tipo_ingreso?' selected':''}}>{{$tingreso->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-8">
                                <button type="submit" class="btn btn-primary">Continuar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
