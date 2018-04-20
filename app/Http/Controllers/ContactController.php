<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use \App\Http\Resources\ContactResource;
use \App\Http\Resources\PersonneResource;
use \App\Http\Resources\EntrepriseResource;
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
            'nom'=>'required',
            'type'=>'in:Personne,Entreprise'
        ]);

       
        if($request->type == null)
        {
            
            $contact =  new \App\Contact($request->all() + ['addedBy' => Auth::user()->id]);
            
        }else
        {
            if($request->type == "Personne")
            {
                $contact =  new \App\Personne($request->all() + ['addedBy' => Auth::user()->id]);
            }else if ($request->type == "Entreprise")
            {
                $contact =  new \App\Entreprise($request->all() + ['addedBy' => Auth::user()->id]);
            }
        }

        if($contact->save())
        {
            return response()->json(["message"=>"Contact Added"],201);   
        }
        
        return response()->json(["message"=>"Server internal Error"],500);        
        
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

    /**
     * it returns all conctacts that have at least one of the relations and are one of the types mentionned
     * if an argument is not passed ( relation or type ), its filter will be ignored 
     * @var types : all types that shoud pass the filter
     * @var relations : all relations that should pass the filter
     * @return contactRessource::collection a filtred list of contacts by relation and type
     */

    public function getContactsFiltred(Request $request)
    {
        $existingTypes = ['Undefined','Entreprise','Personne','User'];
        $existingRelations = \App\Relation::select('relation')->distinct()->pluck('relation')->toArray();
       
        if($request->relations)
        {
             // validation of the relations 
            $relations = \explode(',',$request->relations);
            foreach($relations as $relation)
            {
                if(! \in_array($relation,$existingRelations))
                {
                    return response()->json(["message"=>"Invalide relation value : '$relation' "],400);
                }

            }
            //applying the relations filter to build a query 
            $query = \App\Contact::where(function($query) use ($relations)
            {
                $query->whereHas('relations' ,function ($query) use ($relations)
                {
                    $query->whereIn('relation',$relations);
                });
            });
            
        }else
        {
            //if relations was not passed as an argument we build a query that returns all contacts
            $query = \App\Contact::whereRaw('1');
        }

        
        if($request->types)
        {       
            $types = \explode(',',$request->types);
            //validation of the types 
            foreach($types as $key=>$type)
            {
                if(! \in_array($type,$existingTypes))
                {
                    return response()->json(["message"=>"Invalide type value : '$type' "],400);
                }else if($type == "Personne" || $type == "Entreprise")
                {
                    $types[$key] = "App\\".$type;
                }               
                
            }            
            $query = $query->where(function ($query) use($types)
            {
                //checking for undefined to be tested as contactable_type is NULL
                if(\in_array('Undefined',$types))
                {
                    $query->WhereIn('contactable_type',$types)->orWhereNull('contactable_type');                   
                }else
                {
                    $query->WhereIn('contactable_type',$types);
                } 
                
                //checking for User as it needs a special request to find out who's a user
                if(\in_array("User",$types))
                {                
                    $query->orWhereRaw('id in (SELECT contacts.id from contacts  
                    join personnes on contacts.contactable_id = personnes.id WHERE contacts.contactable_type = "App\\\Personne" and personnes.user_id IS NOT NULL)');
                }
            });
            
        }
        //returning a ressource collection of the results of the query
        return  ContactResource::collection($query->get());
        
    }
}
