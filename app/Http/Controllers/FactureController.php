<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Facture;
use \App\DocumentEntry;
use Auth;
use \App\Http\Resources\FactureResource;


class FactureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {       
        $this->middleware('auth:api');
        
    }

    public function getAll()
    {
        return FactureResource::collection(Facture::paginate());
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
            'entries' => 'required',
            'entries.*.prixVente' =>"required|numeric|min:0",
            'entries.*.prixAchat' =>"numeric|min:0",
            'entries.*.quantiteLivree'=>"numeric|min:0",
            'entries.*.produit_id'=> [function ($attribute, $value, $fail) {
                if (! \App\Produit::find($value)) {
                    $fail(':attribute is an invalid Produit id !');
                }
            }],
            'entries.*.enrgNouveauProduit' => 'boolean',           
            'destNom'=> 'required',
            'destEmail' => 'email',
            'destAssujetiTVA' => 'boolean|required',
            'enrgNouveauClient' => 'boolean'
        ]);
        $fact = new Facture($request->except('entries'));
        $fact->addedBy = Auth::user()->id;
        $result = $fact->save();
       
        
        foreach($request->entries as $entry)
        { 
            if( ! \array_key_exists("produit_id",$entry) && array_key_exists("enrgNouveauProduit",$entry) && $entry['enrgNouveauProduit'])
            {
                $produit = new \App\Produit($entry);
                $produit->estVendu = true;
                $produit->addedBy =  Auth::user()->id;
                $produit->save();
                $produit->setFournisseurs([['prixAchat'=> $entry['prixAchat']]]);
            }
            $line = new DocumentEntry($entry);
            if($produit)
            {
                $line->produit_id = $produit->id;
            }
            $results = $result && $fact->document_entries()->save($line);

           
        }
        if( ! $request->destId && $request->enrgNouveauClient )
        {
            $contact = new \App\Contact();
            $contact->nom = $request->destNom;
            $contact->email = $request->destEmail;
            $contact->adresse = $request->destAdd;
            $contact->addedBy = Auth::user()->id;
            $contact->save();
            $contact->set_relations(['client']);
        }

        if($result)
        {
            return response()->json([
                'message' => 'Facture added successfully',
                'id' => $fact->id,        
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
        $fact = Facture::find($id);
        if($fact)
        {
            return new FactureResource($fact);
        }
        return response()->json(['message'=>'Facture not found'],404);
        
    }


    /**
     * toggle abondonnee from true to false.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleAbandonnee($id)
    {
        $fact = Facture::find($id);
        if($fact)
        {
            $fact->abandonnee = ! $fact->abandonnee;
            $fact->save();
            return response()->json(['message'=>'Facture updated'],200);
        }
        return response()->json(['message'=>'Facture not found'],404);
    }

}
