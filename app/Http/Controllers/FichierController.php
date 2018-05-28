<?php

namespace App\Http\Controllers;
use App\Fichier;
use App\Tache;
use Auth;
use File;
use App\Http\Resources\FichierResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FichierController extends Controller
{

    public function __construct()
    {       
        $this->middleware('auth:api');
        
    }
    protected $directory = 'Fichiers_Taches';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        $query = Fichier::whereHas('tache',function($query)
        {
            $query->where('visibleAuxAutres',true);
        })->orWhere('addedBy',Auth::user()->id);

        return FichierResource::collection($query->paginate());
    }

    
    /**
     * searches for a file by nom and date interval.
     * both created_at and updated_at are taken into consideration in the resaerch
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $this->validate($request,[
            'nom'=> 'string',
            'startDate' => 'date',
            'endDate' => 'date'
        ]);
        $query = Fichier::where(function($query) use ($request){

            $query->whereHas('tache',function($query)
            {
                $query->where('visibleAuxAutres',true)->orWhere('fichiers.addedBy',Auth::user()->id);
            });
            if($request->nom)
            {
                $query->where('nom','like',"%$request->nom%");
            }
            if($request->startDate)
            {
                $query->where(function($query) use ($request) {
                    $query->where('updated_at','>=',$request->startDate)->orWhere('created_at','>=',$request->startDate);
                });
                
            }
            if($request->endDate)
            {
                $query->where(function($query) use ($request) {
                    $query->where('updated_at','<=',$request->endDate)->orWhere('created_at','<=',$request->endDate);
                });
            }
        });

        return FichierResource::collection($query->paginate());
    }

    /**
     * downloads a file of a fichier
     * @param int $id : id of the fichier
     * only if the user is allowed to view fichier, that he can download the file.
     * @return file 
     */
    public function download($id)
    {
        $fichier = Fichier::find($id);
        
        if(! $fichier)
        {
            return response()->json(['message'=>'fichier not found'],404);
        }
        if( ! $fichier->tache->isVisibleTo(Auth::user()->id) && $fichier->addedBy != Auth::user()->id)
        {
            return response()->json(['message'=>
                'Invalid Operation : this fichier belongs to another user and to a tache that is not visible by this user'],405);
        }
        return response()->download(storage_path('app/'.$fichier->chemin));

    }

   
    /**
     * gets all Fichiers of a tache
     * @param int $tacheId : id of the tache
     * the tache has to be visible to the user so that the files are returned
     * @return FichierResource::collection
     */
    public function getAllOfATache($tacheId)
    {
        $tache = Tache::find($tacheId);
        if( ! $tache)
        {
            return response()->json(["message"=>"No Tache with id = $tacheId found"],404);
        }
        if( ! $tache->isVisibleTo(Auth::user()->id))
        {
            return response()->json(["message"=>"Invalid operation : this Tache can't be seen by this user"],405);
        }
        return FichierResource::collection($tache->fichiers()->paginate());

    }
    /**
     * saves a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        $this->validate($request,[
            'file' => 'required | file',
            'nom' => 'required|max:255', // only a logical name not the one saved to the database 
            'tache_id' => [function ($attribute, $value, $fail) {
                if (! Tache::find($value)) {
                    $fail(':attribute is an invalid tache id !');
                }else if (!Tache::find($value)->isVisibleTo(Auth::user()->id))
                {
                    $fail(':attribute is not a visibile tache id to this user !');
                }
            },'required'],
        ]);
        
        //the file name will be automatically generated
        $fichier = new Fichier($request->all());
        $fichier->addedBy = Auth::user()->id;
        $fichier->chemin = $request->file->store($this->directory);

        if($fichier->save())
        {
            return response()->json(['message'=> 'fichier added succefully',
                    'id'=>$fichier->id],
                200);
        }
        return response()->json(['message'=>'internal server error'],500);


    }

    /**
     * return the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($id)
    {
        $fichier = Fichier::find($id);
        if(! $fichier)
        {
            return response()->json(['message'=>'fichier not found'],404);
        }
        if( ! $fichier->tache->isVisibleTo(Auth::user()->id) && $fichier->addedBy != Auth::user()->id)
        {
            return response()->json(['message'=>'Invalid Operation : this fichier belongs to another user and to a tache that is not visible by this user'],405);
        }
        return new FichierResource($fichier);
    }

    /**
     * Update the specified resource in storage.
     * if another file is uploaded, the old one is deleted
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $fichier = Fichier::find($id);
        if( ! $fichier)
        {
            return response()->json(['message'=>'fichier not found'],404);
        }
        if( $fichier->addedBy != Auth::user()->id)
        {
            return response()->json(['message'=>'Invalid Operation : a fichier can be edited only by the user who added it'],405);
        }

        $this->validate($request,[
            'file' => 'file',
            'nom' => 'max:255', // only a logical name not the one saved to the database 
            'tache_id' => [function ($attribute, $value, $fail) {
                if (! Tache::find($value)) {
                    $fail(':attribute is an invalid tache id !');
                }else if (!Tache::find($value)->isVisibleTo(Auth::user()->id))
                {
                    $fail(':attribute is not a visibile tache id to this user !');
                }
            }],
        ]);
        if($request->file)
        {   Storage::delete($fichier->chemin);
            $fichier->addedBy = Auth::user()->id;
            $fichier->chemin = $request->file->store($this->directory);
        }
        $fichier->update($request->all());
        if($fichier->save())
        {
            return response()->json(['message'=> 'fichier updated succefully',
                    'id'=>$fichier->id],
                200);
        }
        return response()->json(['message'=>'internal server error'],500);

    }

    /**
     * Remove the specified resource from storage.
     * only the user who added the file and the super user can delete it.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $fichier = Fichier::find($id);
        if( ! $fichier)
        {
            return response()->json(['message'=>'fichier not found'],404);
        }
        if( $fichier->addedBy != Auth::user()->id && ! Auth::user()->superUser)
        {
            return response()->json(['message'=>'Invalid Operation : a fichier can be deleted only by the user who added it or a super user'],405);
        }
        if ($fichier->delete())
        {
            return response()->json(['message'=> 'fichier deleted succefully',
                    'id'=>$fichier->id],
                200);
        }
        return response()->json(['message'=>'internal server error'],500);
    }
}
