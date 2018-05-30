<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\Http\Resources\CategorieResource;
use \App\Categorie;



class CategorieController extends Controller
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
        return CategorieResource::collection(Categorie::paginate());
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
        $categorie = new Categorie($request->all());
        $categorie->addedBy = Auth::user()->id;
        if($categorie->save())
        {
            return response()->json([
                        'message' => 'categorie added successfully',
                        'id' => $categorie->id,        
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
        $categorie = Categorie::find($id);
        if($categorie)
        {
            return new CategorieResource($categorie);
        }
        return response()->json(['message'=>'categorie not found'],404);
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
        $categorie = Categorie::find($id);
        if(!$categorie)
        {
            return response()->json(['message'=>'categorie not found'],404);
        }
        if($categorie->addedBy != Auth::user()->id && ! Auth::user()->superUser)
        {
            return response()->json(["Invalid Operation : only a super user and the user who created this category can edit it"],405);
        }
        $categorie->update($request->all());
        if($categorie->save())
        {
            return response()->json(["categorie updated succefully"],200);
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
            return response()->json(['message'=>'Invalid ID : default categorie cant be deleted'],400);
        }
        $categorie = Categorie::find($id);
        if(!$categorie)
        {
            return response()->json(['message'=>'categorie not found'],404);
        }
        if($categorie->addedBy != Auth::user()->id && ! Auth::user()->superUser)
        {
            return response()->json(["Invalid Operation : only a super user and the user who created this category can delete it"],405);
        }
        foreach($categorie->produits as $produit)
        {
            $produit->categorie_id = 1;
            $produit->save();
        }
        if($categorie->delete())
        {
            return response()->json(["categorie deleted succefully"],200);
        }
        return response()->json(['message' => 'Internal Server Error'],500);
    }
}
