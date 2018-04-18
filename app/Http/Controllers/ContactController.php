<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use \App\Http\Resources\ContactResource;
use \App\Contact;

class ContactController extends Controller
{
    public function __construct()
    {
       
        $this->middleware('auth:api');
     
    }

    public function saveContact(Request $request)
    {
        $this->validate($request,[
            'nom'=>'required'
        ]);

        if(Auth::check())
        {
            $contact =  new \App\Contact($request->all() + ['addedBy' => Auth::user()->id]);
            $contact->save();
            return response()->json(["message"=>"Contact Added"],201);        
        }
       
        return redirect()->json(["message"=>"No user logged in"],405); 
    }

    public function updateContact(Request $request, $id)
    {
        $this->validate($request,[
            'nom'=>'required'
        ]);   
        $contact =  Contact::find($id);
        $contact->update($request->all()); 
        if($contact->save())
        {
            return response()->json(["message"=>"Contact updated"],201);
        }
                
      
        return response()->json(["message"=>"Error : contact can't be updated"],405); 
    }

    public function getAllContacts()
    {       
        return   ContactResource::collection(\App\Contact::get());
    }
    public function getContact($id)
    {
        return new ContactResource(Contact::find($id));
    }
    public function deleteContact($id)
    {
        $contact = Contact::find($id);
        
        if($contact)
        {
            $contact->delete();
           return response()->json(["message"=>"contact deleted"],200);
        }
        return response()->json(["message"=>"can't find contact with id : $id"],404);
    }
    /**
     * retrieves people working at a company
     * TODO not tested yet
     * needs to figure out a wa to get company id
     */

    public function getContactsWorkingAt($companyContactId)
    {
        // retrieves an object of type Entreprise
        $company = Contact::find($companyContactId)->contactable;
        if($company)
        {
            return   ContactResource::collection($company->employees()->get());

        }
        return response()->json(["message"=>"can't find  company contact with id : $id"],404);
    }
}
