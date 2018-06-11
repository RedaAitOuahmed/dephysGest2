<?php

namespace App\Http\Controllers;
use App\Produit;
use App\Http\Resources\ProduitResource;
use Auth;

use Illuminate\Http\Request;

class ProduitController extends Controller
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
        return ProduitResource::collection(Produit::Paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    private function validateRequest(Request $request)
    {
        $this->validate($request,[
            'prixVenteHT' => 'numeric|min:0',
            'TVA' => 'numeric|min:0',
            'prixVenteTTC' => 'numeric|min:0',
            'estAchete' => 'boolean',
            'estVendu' => 'boolean',
            'categorie_id' => [function ($attribute, $value, $fail) {
                if (! \App\Categorie::find($value)) {
                    $fail(':attribute is an invalid Categorie id !');
                }
            }],
            'fournisseurs.*.id' => [function ($attribute, $value, $fail) {
                if (! \App\Contact::find($value)) {
                    $fail(':attribute is an invalid Contact id !');
                }
            }],
            'fournisseurs.*.prixAchatHT' => 'numeric|min:0',
            'fournisseurs.*.TVA_achat' => 'numeric|min:0',
            'fournisseurs.*.prixAchatTTC' => 'numeric|min:0',
        ]);

    }
    public function save(Request $request)
    { 
        $this->validate($request,[
            'nom' => 'required|unique:produits',
            'codeBarre' => 'unique:produits',
            ]);     
        $this->validateRequest($request);
        $produit = new Produit($request->all());
        $produit->addedBy = Auth::user()->id;
        
        if($produit->save())
        {
            if($request->fournisseurs)
            {
                $produit->setFournisseurs($request->fournisseurs);
            }            
            return response()->json([
                        'message' => 'produit added successfully',
                        'id' => $produit->id,        
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
        $produit = Produit::find($id);
        if($produit)
        {
            return new ProduitResource($produit);
        }
        return response()->json(['message'=>'Produit not found'],404);
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
        // unique checks for all rows except the one corresponding to this id
        $this->validate($request,[
            'nom' => 'unique:produits,nom,'.$id,
            'codeBarre' => 'unique:produits,codeBarre,'.$id,
            ]); 
        $this->validateRequest($request);
        $produit = Produit::find($id);
        if(!$produit)
        {
            return response()->json(['message'=>'Produit not found'],404);
        }
        if($produit->addedBy != Auth::user()->id && ! Auth::user()->superUser)
        {
            return response()->json(["Invalid Operation : only a super user and the user who added this Produit can edit it"],405);
        }
        $produit->update($request->all());
        if($produit->save())
        {
            if($request->fournisseurs)
            {
                $produit->setFournisseurs($request->fournisseurs);
            }            
            return response()->json(["Produit updated succefully"],200);
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
        $produit = Produit::find($id);
        if(!$produit)
        {
            return response()->json(['message'=>'Produit not found'],404);
        }
        if($produit->addedBy != Auth::user()->id && ! Auth::user()->superUser)
        {
            return response()->json(['message' =>"Invalid Operation : only a super user and the user who added this Produit can delete it"],405);
        }
        /*
        WHAT ABOuT FACTURES AND BONS DE COMMANDES CONTAINING THIS PRODUCT
        */
        if($produit->delete())
        {
            return response()->json(['message' =>"produit deleted succefully"],200);
        }
        return response()->json(['message' => 'Internal Server Error'],500);
    }


    public function getProduitsFiltred(Request $request)
    {
        /*
         All products that belong to one of these categories
         AND are yes or no bought AND  yes or no sold
         AND are added by currentUser if set to true, added by otherUsers if set to false 
         AND their nom is like %searchArgument% OR their codeBarre  is  like %searchArgument%  
        */
        $this->validate($request,[
           
            'categorieIds.*' =>[function ($attribute, $value, $fail) {
                if (! \App\Categorie::find($value)) {
                    $fail(':attribute is an invalid Categorie id !');
                }
            }],
            'estAchete' => 'boolean',
            'estVendu' => 'boolean',
            'addedByCurrentUser'  => 'boolean',   
        ]);

        $query = Produit::where(function($query) use ($request)
        {
            
            if($request->categorieIds)
            {
                $query->whereIn('categorie_id',$request->categorieIds);
            }

            if( $request->estAchete !== null)
            {
                $query->where('estAchete','=',$request->estAchete);
            }
            if ($request->estVendu !== null)
            {
                $query->where('estVendu',"=",$request->estVendu);
            }
               
            
            if($request->addedByCurrentUser !== null)
            {
                if($request->addedByCurrentUser === true)
                {
                    $query->where('addedBy','=',Auth::user()->id);
                }else
                {
                    $query->where('addedBy','!=',Auth::user()->id);
                }
            }
            if($request->searchArgument)
            {
                $query->where('nom','like','%'.$request->searchArgument.'%')->orWhere('codeBarre','like','%'.$request->searchArgument.'%');
            }
            
        });
             

        return ProduitResource::collection($query->paginate());
    }
}
