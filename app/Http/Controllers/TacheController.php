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


    public function save(Request $request)
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
    public function getAll()
    {
        $query = Tache::where('visibleAuxAutres',true)->orWhere('addedBy',Auth::user()->id);
        return TacheResource::collection($query->get());
    }


    public function update(Request $request, $tacheId)
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


    public function get($tacheId)
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


    public function delete($tacheId)
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

    /**
     * only a super user can delete all taches of a projet
     */
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
    // the tasks that the user assigned are basically the ones he created
    /**
     * filters tache based on 3 filters : 
     * @param visibility
     * @param assignation
     * @param projetIds
     * a logical AND is performed between the filters, if a filter value is null then it's ignored
     */
    public function getTachesFiltred(Request $request)
    {
        $this->validate($request,[
            'visibility' =>'in:perso,public',
            'assignation' => 'in:userAssigned,assignedToUser',
            'projetIds.*' => [function ($attribute, $value, $fail) {
                if (! \App\Projet::find($value)) {
                    $fail(':attribute is an invalid projet id !');
                }
            }],
        ]);

        $query = Tache::where(function($query) use ($request)
        {
            if($request->visibility == 'perso')
            {
                $query->where('addedBy','=',Auth::user()->id)->where('visibleAuxAutres','=',false);
            }else if ($request->visibility == 'public')
            {
                $query->where('visibleAuxAutres','=',true);
            }else
            {
                $query->where(function($query){
                    $query->where('visibleAuxAutres',true)->orWhere('addedBy',Auth::user()->id);
                });
            }


            if($request->assignation == 'userAssigned')
            {
                $query->where('addedBy','=',Auth::user()->id);
            }else if ($request->assignation == 'assignedToUser')
            {
                $query->whereHas('assigned_to', function ($query) {
                    $query->where('users.id', '=', Auth::user()->id);
                });
            }

            if($request->projetIds)
            {
                $query->whereIn('projet_id',$request->projetIds);
            }
        });

        return TacheResource::collection($query->get());
    }

}
