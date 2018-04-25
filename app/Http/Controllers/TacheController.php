<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Tache;
use \App\Projet;
use \App\Http\Resources\TacheResource;
use Auth;

class TacheController extends Controller
{
    public function __construct()
    {       
        $this->middleware('auth:api');
     
    }
    protected function validateRequest(Request $request)
    {
        $this->validate($request, [
            'nom' => 'required',
            'visibleAuxAutres' => 'required | boolean',
            'dateLimite' => 'nullable|date',
            'projetId' => [function ($attribute, $value, $fail) {
                if (! \App\Projet::find($value)) {
                    $fail(':attribute is an invalid projet id !');
                }
            }],
            'etat' => 'in:aFaire,enCours,Faite',
            'assignedTo.*' => [function ($attribute, $value, $fail) {
                $contact = \App\Contact::find($value);
                if ( !$contact || ! $contact->isUser()) {
                    $fail(':attribute is not an id of a user !');
                }
            }],
        ]);
    }

    public function saveTache(Request $request)
    {
        $this->validateRequest($request);

        $tache =  new \App\Tache($request->all());
        $tache->addedBy = Auth::user()->id;
        if($tache->save())
        {
            if($request->assignedTo)
            {
                $tache->setAssignations($request->assignedTo);
            }
            return response()->json(["message"=>"Tache Added", "id"=> $tache->id],201); 
        }
        
        return response()->json(["message"=>"Server internal Error"],500);     
        
    }
    /**
     * @return TacheResource::collection a list of all taches visible to the user making the request 
     */
    public function getAllTaches()
    {
        $query = Tache::where('visibleAuxAutres',true)->orWhere('addedBy',Auth::user()->id);
        return TacheResource::collection($query->get());
    }
    public function updateTache(Request $request, $tacheId)
    {
        $tache = Tache::find($tacheId);
        if( ! $tache)
        {
            return response()->json(["message"=>"No Tache with id = $tacheId found"],404);
        }
        if( ! $tache->editableBy(Auth::user()->id))
        {
            return response()->json(["message"=>"Invalid Operation : this Tache can't be edited by this user, as it's not assigned to him nor he was he the one who created it neither is he a super user"],405);
        }
        $this->validateRequest($request);

        if($request->assignedTo)
        {
            $tache->setAssignations($request->assignedTo);
        }
        $tache->update($request->all());
        if($tache->save())
        {
            return response()->json(["message"=>"Tache updated", "id"=> $tache->id],201); 
        }
        
        return response()->json(["message"=>"Server internal Error"],500);

    }
    public function getTache($tacheId)
    {
        $tache = Tache::find($tacheId);
        if( ! $tache)
        {
            return response()->json(["message"=>"No Tache with id = $tacheId found"],404);
        }
        if( ! $tache->isVisibleTo(Auth::user()->id))
        {
            return response()->json(["message"=>"this Tache can't be seen by this user"],405);
        }

        return new TacheResource($tache);

    }
    public function deleteTache($tacheId)
    {
        $tache = Tache::find($tacheId);
        if( ! $tache)
        {
            return response()->json(["message"=>"No Tache with id = $tacheId found"],404);
        }
        if( $tache->addedBy  != Auth::user()->id || ! Auth::user()->superUser)
        {
            return response()->json(["message"=>"Invalid Operation : only a super user or the user who created this tache can delete it"],405);
        }
        if($tache->delete())
        {
            return response()->json(["message"=>"Tache deleted"],200);
        }
        return response()->json(["message"=>"Server internal Error"],500); 

    }

    public function getTachesOfAProjet($projetId)
    {
        $projet = Projet::find($projetId);
        if( ! $projet)
        {
            return response()->json(["message"=>"No Projet with id = $projetId found"],404);
        }
        $query = Projet::where('id',$projetId)->first()->taches()->where(function ($query) 
        {
            $query->where('visibleAuxAutres',true)->orWhere('addedBy',Auth::user()->id);
        });
        
        return TacheResource::collection($query->get());
    }
    public function deleteTachesOfAProjet($projetId)
    {
        $projet = Projet::find($projetId);
        if( ! $projet)
        {
            return response()->json(["message"=>"No Projet with id = $projetId found"],404);
        }
        if( ! Auth::user()->superUser)
        {
            return response()->json(["message"=>"Invalid Operation : this user is not a super user, only a super user can perform this action"],405);
        }
        if($projet->taches()->count() == 0)
        {
            return response()->json(["message"=>"No taches to delete"],501);
        }
        if($projet->taches()->delete())
        {
            return response()->json(["message"=>"Succeded Operation, all 'taches' of the 'projet' were deleted"],200);
        }
        return response()->json(["message"=>"Internal Server Error"],500);
    }

    public function getTachesFiltred(Request $request)
    {
        $this->validate($request,[
            'visibility' =>'in:perso,public',
            'assignation' => 'in:userAssigned,addignedToUser',
            'projetIds.*' => [function ($attribute, $value, $fail) {
                if (! \App\Projet::find($value)) {
                    $fail(':attribute is an invalid projet id !');
                }
            }],
        ]);
        return response()->json(['message'=>$request->all()],200);
    }

}
