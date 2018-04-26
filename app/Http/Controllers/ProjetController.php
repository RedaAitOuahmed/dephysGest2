<?php

namespace App\Http\Controllers;
use \App\Projet;
use Auth;
use App\Http\Resources\ProjetResource;
use Illuminate\Http\Request;


class ProjetController extends Controller
{
    public function __construct()
    {       
        $this->middleware('auth:api');
     
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        return ProjetResource::collection(Projet::paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        $this->validate($request,[
            'nom' => 'required'
        ]);
        $projet = new Projet($request->all());
        $projet->addedBy = Auth::user()->id;
        if($projet->save())
        {
            return response()->json([
                        'message' => 'projet added successfully',
                        'id' => $projet->id,        
                    ]
                ,200);
        }
        return response()->json(['message' => 'Internal Server Error'],500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($id)
    {
        $projet = Projet::find($id);
        if($projet)
        {
            return new ProjetResource($projet);
        }
        return response()->json(['message'=>'projet not found'],404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $projet = Projet::find($id);
        if(!$projet)
        {
            return response()->json(['message'=>'projet not found'],404);
        }
        if($projet->addedBy != Auth::user()->id && ! Auth::user()->superUser)
        {
            return response()->json(["Invalid Operation : only a super user and the user who created this project can edit it"],405);
        }
        $projet->update($request->all());
        if($projet->save())
        {
            return response()->json(["projet updated succefully"],200);
        }
        return response()->json(['message' => 'Internal Server Error'],500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if($id == 1)
        {
            return response()->json(['message'=>'Invalid ID : default projet cant be deleted'],400);
        }
        $projet = Projet::find($id);
        if(!$projet)
        {
            return response()->json(['message'=>'projet not found'],404);
        }
        if($projet->addedBy != Auth::user()->id && ! Auth::user()->superUser)
        {
            return response()->json(["Invalid Operation : only a super user and the user who created this project can delete it"],405);
        }
        foreach($projet->taches as $tache)
        {
            $tache->projet_id = 1;
            $tache->save();
        }
        if($projet->delete())
        {
            return response()->json(["projet deleted succefully"],200);
        }
        return response()->json(['message' => 'Internal Server Error'],500);
    }
}
