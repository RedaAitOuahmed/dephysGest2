<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use \App\Http\Resources\ContactResource;

class Contact extends Controller
{
    public function __construct()
    {
       
        $this->middleware('auth');
     
    }
    public function add()
    {        
        return view('addContact');
    }
    public function addSubmit(Request $request)
    {
        $this->validate($request,[
            'nom'=>'required'
        ]);
        if(Auth::check())
        {
            $contact =  new \App\Contact($request->all() + ['addedBy' => Auth::user()->id]);
            $contact->save();
            return "Contact Added";        
        }
       
        return redirect()->route('home');
    }
    public function displayAll()
    {       
        return   ContactResource::collection(\App\Contact::get());
    }
}
