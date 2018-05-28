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

    public function save(Request $request)
    {
        
        $this->validate($request,[
            'nom'=>'required',
            'type'=>'in:Personne,Entreprise',
            'relations.*'=>'in:client,fournisseur,collegue,prospect',
            'email' => 'email',
        ]);

       
        if($request->type == null)
        {
            
            $contact =  new \App\Contact($request->all() );
            $contact->addedBy = Auth::user()->id;
            
        }else
        {
            if($request->type == "Personne")
            {
                $contact =  new \App\Personne($request->all() );
                $contact->addedBy = Auth::user()->id;
            }else if ($request->type == "Entreprise")
            {
                $contact =  new \App\Entreprise($request->all() );
                $contact->addedBy = Auth::user()->id;
            }
        }

        if($contact->save())
        {
            //setting the contact to be a contact instance
            $contact = $contact->contacts()->first();
            if($request->relations)
            {
                $contact->set_relations($request->relations);
            }
            
            return response()->json(["message"=>"Contact Added", "id"=> $contact->id],201);   
        }
        
        return response()->json(["message"=>"Server internal Error"],500);        
        
    }

    public function update(Request $request, $id)
    {
        $contact =  Contact::find($id);

        if( ! $contact)
        {
            return response()->json(["message"=>"No contact with id = $id found"],404);
        }
        if( !Auth::user()->superUser && $contact->addedBy != Auth::user()->id)
        {
            return response()->json(["message"=>"Invalid Operation : can be edited only by a super user or by the user who added this contact"],410);
        }

        $this->validate($request,[
            'nom'=>'required',
            'type'=>'in:Personne,Entreprise',
            'relations.*'=>'in:client,fournisseur,collegue,prospect',
            'email' => 'email',
        ]);

        if($request->type == 'Entreprise' && $contact->getType() == 'User')
        {
            return response()->json(["message"=>"Invalid Operation : can't cast User type to Entrerpise type"],400);
        }
        //first we update the relations
        if($request->relations)
        {
            $contact->set_relations($request->relations);
        }
        
                
        // case when we're not changing the type of the contact :
        if($request->type == null || $request->type == $contact->getType() || ($request->type == 'Personne' && $contact->getType() == 'User') )
        { 
            // to update the contact instance
            $contact->update($request->all()); 
            if($contact->contactable)
            {
                // to update the contactable instance ( Personne ou Entreprise)
                $contact = $contact->contactable;
                $contact->update($request->all());
            }
             
            if($contact->save())
            {
                return response()->json(["message"=>"Contact updated"],201);
            }
            return response()->json(["message"=>"Server Internal Error"],500);

        }else
        {
            if($contact->contactable != null)
            {
                $oldContactableInstance = $contact->contactable;

            }
            //first a personne or Entreprise instance is created
            $contactableInstance;
            if($request->type == "Personne")
            {
                $contactableInstance = new \App\Personne();
            }else
            {
                $contactableInstance = new \App\Entreprise();
            }
            // we save the contactable instance to the database
            $res = $contactableInstance->save();
            // so that we can associate the contact instance to the contactable instance
            $contact->contactable()->associate($contactableInstance);
            // we save the changes that happend to contact to the database
            $res = $res && $contact->save();

            // we finnally update the contact and contactable instances.            
            $res = $res && $contact->update($request->all());
            $res = $res && $contactableInstance->update($request->all());
            if($res)
            {
                return response()->json(["message"=>"Contact updated"],201);
            }else
            {
                return response()->json(["message"=>"Server Internal Error"],500);
            }
            //at last we check if oldContactableInstance is not null so that we delete it
            if($oldContactableInstance)
            {
                if( ! $oldContactableInstance->delete())
                {
                    return response()->json(["message"=>"Warning old contactable instance couldn't be delted "],207);
                }
            }
        

        }
             
    }

    public function getAll()
    {       
        return   ContactResource::collection(\App\Contact::paginate());
    }
    public function get($id)
    {
        $contact =  Contact::find($id);

        if( ! $contact)
        {
            return response()->json(["message"=>"No contact with id = $id found"],404);
        }
        return new ContactResource($contact);
    }
    public function delete($id)
    {
        $contact = Contact::find($id);
        if( !Auth::user()->superUser && $contact->addedBy != Auth::user()->id)
        {
            return response()->json(["message"=>"Invalid Operation : can be deleted only by a super user or by the user who added this contact"],410);
        }
        
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
        $contact =  Contact::find($companyContactId);

        if( ! $contact)
        {
            return response()->json(["message"=>"No contact with id = $companyContactId found"],404);
        }
        // retrieves an object of type Entreprise
        $company = $contact->contactable;
        if($company && \get_class($company) == 'App\Entreprise')
        {
            return   ContactResource::collection($company->employees()->paginate());

        }
        return response()->json(["message"=>"Invalid Operation : Contact id is not of an Entreprise"],405);
    }

    /**
     * it returns all conctacts that have at least one of the relations and are one of the types mentionned
     * if an argument is not passed ( relation or type ), its filter will be ignored 
     * @var types : all types that shoud pass the filter
     * @var relations : all relations that should pass the filter
     * @return ContactResource::collection a filtred list of contacts by relation and type
     */

    public function getContactsFiltred(Request $request)
    {
        $existingTypes = ['Undefined','Entreprise','Personne','User'];
        $existingRelations = \App\Relation::select('relation')->distinct()->pluck('relation')->toArray();
        $existingRelations = \array_merge($existingRelations, ['client','fournisseur','prospect','collegue']);
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
        return  ContactResource::collection($query->paginate());
        
    }
}
