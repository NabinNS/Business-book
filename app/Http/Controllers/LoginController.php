<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function Login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            if (auth()->user()->role == 'marketing') {
                return redirect('parties');
            } else {
                return redirect('dashboard');
            }
        }
        return redirect("/")->with('error', 'Username or Password not matched');
    }
    public function logout()
    {
        Session::flush();

        Auth::logout();

        return redirect('/');
    }
}
