<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    //protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('guest')->except('logout');

    }

    public function authenticated(Request $request,$user )
    {
        /*if($request->user()->authorizeRoles('escolares')){
            return redirect()->route('inicio_escolares') ;
        }elseif ($request->user()->authorizeRoles('alumno')){
            return redirect()->route('inicio_alumno') ;
        }*/
        if($user->hasRole('escolares')){
            return redirect('/escolares') ;
        }
        if ($user->hasRole('alumno')){
            return redirect('/estudiante') ;
        }
        if ($user->hasRole('docente')){
            return redirect('/personal') ;
        }
        if ($user->hasRole('verano')){
            return redirect('/verano') ;
        }
        if ($user->hasRole('division')){
            return redirect('/dep') ;
        }
        if ($user->hasRole('acad')){
            return redirect('/acad') ;
        }
        if ($user->hasRole('planeacion')){
            return redirect('/planeacion') ;
        }
        if ($user->hasRole('direccion')){
            return redirect('/direccion') ;
        }
        if ($user->hasRole('desacad')){
            return redirect('/desacad') ;
        }
    }
}
