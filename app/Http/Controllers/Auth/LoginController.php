<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
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

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login()
    {
        if(!Auth::attempt(request(['email', 'password']))){
            return redirect('/login')->withInput(request(['email']))->withErrors([
                'email' => ['These credentails do not match our records.']
            ]);
        }

        if(Auth::user()->approved != 1) {
            Auth::logout();
            return redirect('/login')->withInput(request(['email']))->withErrors([
                'email' => ['Your account is pending approval']
            ]);
        }

        if(Auth::user()->blocked) {
            Auth::logout();
            return redirect('/login')->withInput(request(['email']))->withErrors([
                'email' => ['Your account has been blocked']
            ]);
        }

        if(Auth::user()->is_admin == 1) {
            return redirect('/admin');
        }

        return redirect('/');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

}
