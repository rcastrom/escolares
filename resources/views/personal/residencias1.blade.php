@extends('layouts.docentes')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Módulo Personal Docente</div>
                <div class="card-body">
                    <h3>Evaluación residencias profesionales</h3>
                    <form action="{{route('personal_residencias1')}}" method="post" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="per_res">Señale el período a evaluar</label>
                            <select name="per_res" id="per_res" required class="form-control">
                                @foreach($periodos as $per)
                                    <option value="{{$per->periodo}}" {{$per->periodo==$periodo?' selected':''}}>{{$per->identificacion_larga}}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Continuar</button>
                    </form>
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
