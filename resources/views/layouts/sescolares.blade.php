<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- CSS -->
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css')}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{ asset('plugins/jqvmap/jqvmap.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css')}}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css')}}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css')}}">
    <link href="{{ asset('css/reticula.css') }}" rel="stylesheet">

</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{url('/escolares')}}" class="nav-link">Home</a>
            </li>
        </ul>
    </nav>
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="{{url('/escolares')}}" class="brand-link">
            <img src="{{asset('img/escudo.jpg')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">SII 2.0</span>
        </a>
        <!--   Sidebar -->
        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="{{asset('img/escolares.png')}}" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block">{{ Auth::user()->name }}</a>
                </div>
            </div>
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-user-friends"></i><p>Alumnos<i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview collapse">
                            <li class="nav-item">
                                <a href="{{url('/escolares/alumnos/consulta')}}" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Consulta</p></a>
                            </li>
                            <li class="nav-item">
                                <a href="{{url('/escolares/alumnos/alta')}}" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Alta</p></a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="far fa-calendar-alt"></i><p>Períodos<i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{url('/escolares/periodos/alta')}}" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Creación</p></a>
                            </li>
                            <li class="nav-item">
                                <a href="{{url('/escolares/periodos/modifica')}}" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Modificación</p></a>
                            </li>
                            <li class="nav-item">
                                <a href="{{url('/escolares/reinscripcion')}}" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Reinscripción</p></a>
                            </li>
                            <li class="nav-item">
                                <a href="{{url('/escolares/cierre')}}" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Cierre de semestre</p></a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-square-root-alt"></i><p>Actas<i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{url('/escolares/actas')}}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i><p>Actas</p></a>
                            </li>
                            <li class="nav-item">
                                <a href="{{url('/escolares/actas/registro')}}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i><p>Registro</p></a>
                            </li>
                            <li class="nav-item">
                                <a href="{{url('/escolares/actas/mantenimiento')}}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i><p>Mantenimiento</p></a>
                            </li>
                            <li class="nav-item">
                                <a href="{{url('/escolares/actas/foliado')}}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i><p>Foliado</p></a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-chalkboard-teacher"></i><p>Carreras<i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{url('/escolares/carreras/alta')}}" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Alta</p></a>
                            </li>
                            <li class="nav-item">
                                <a href="{{url('/escolares/carreras/especialidades')}}" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Especialidades</p></a>
                            </li>
                            <li class="nav-item">
                                <a href="{{url('/escolares/carreras/materias')}}" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Materias</p></a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-language"></i><p>Idiomas<i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{url('/escolares/idiomas/liberacion')}}" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Liberación</p></a>
                            </li>
                            <li class="nav-item">
                                <a href="{{url('/escolares/idiomas/impresion')}}" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Imprimir</p></a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-chart-pie"></i><p>Estadística<i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{url('/escolares/estadistica/prepoblacion')}}" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Población</p></a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a data-toggle="tooltip" data-placement="top" title="Logout" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <span class="glyphicon glyphicon-off" aria-hidden="true">{{ __('Salir') }}</span>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- /Sidebar Menu -->
        </div>
        <!-- /Sidebar -->
    </aside>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12">
                        <h1 class="m-0 text-dark">Bienvenid@</h1>
                    </div><!-- /.col -->
                    <!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->
        <section class="content">
            @yield('content')
        </section>
        <!-- /.content -->
    </div>
    <footer class="main-footer">
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 2.0
        </div>
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{asset('plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- ChartJS -->
<script src="{{ asset('plugins/chart.js/Chart.min.js')}}"></script>
<!-- Sparkline -->
<script src="{{ asset('plugins/sparklines/sparkline.js')}}"></script>
<!-- JQVMap -->
<script src="{{ asset('plugins/jqvmap/jquery.vmap.min.js')}}"></script>
<script src="{{ asset('plugins/jqvmap/maps/jquery.vmap.usa.js')}}"></script>
<!-- jQuery Knob Chart -->
<script src="{{ asset('plugins/jquery-knob/jquery.knob.min.js')}}"></script>
<!-- daterangepicker -->
<script src="{{ asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
<!-- Summernote -->
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js')}}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('dist/js/adminlte.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('dist/js/demo.js')}}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{ asset('dist/js/pages/dashboard.js')}}"></script>
</body>
</html>


