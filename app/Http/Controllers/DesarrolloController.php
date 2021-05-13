<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DesarrolloController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(){
        return view('desarrollo.inicio');
    }
    /*Para fichas*/
    public function fichas_inicio(){
        return view('desarrollo.fichas_inicio');
    }
    /*Termina para fichas */
}
