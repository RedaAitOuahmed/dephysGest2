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

        $tache =  new \App\Tache($request->all() + ['addedBy' => Auth::user()->id]);
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
            return response()->json(["message"=>"this Tache can't be edited by this user"],405);
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
        if( $tache->addedBy  != Auth::user()->id)
        {
            return response()->json(["message"=>"this tache was created by another user"],405);
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
        return TacheResource::collection($projet->taches);
    }
    // public function deleteAllTachesOfAProjet($projetId)
    // {
    //     $projet = Projet::find($projetId);
    //     if( ! $projet)
    //     {
    //         return response()->json(["message"=>"No Projet with id = $projetId found"],404);
    //     }
    //     return TacheResource::collection($projet->taches);
    // }

}
