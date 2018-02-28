<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class AdminLoginController extends Controller
{
    public function __construct()
    {
         $this->middleware('guest:admin');
    }

    public function loginForm()
    {
        return view('auth.admin-login');
    }

    public function login(Request $request)
    {
        $this->validate($request,[
            'email'=>'required|email',
            'password'=>'required'
        ]);

        
        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password]))
        {
            return redirect()->intended(route('adminHome'));
        }


        return redirect()->back()->withInput($request->only('email'));
        
    }
}
