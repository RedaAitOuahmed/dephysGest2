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
            'entries.*.nom' => 'required',

            'entries.*.TVA_achat' =>"numeric|min:0",
            'entries.*.prixAchatHT' =>"numeric|min:0",
            'entries.*.prixAchatTTC' =>"numeric|min:0|greater_than_or_equal_field:entries.*.prixAchatHT",
            
            'entries.*.TVA_vente' =>"numeric|min:0|required_without:entries.*.prixVenteHT,entries.*.prixVenteTTC",
            'entries.*.prixVenteHT' =>"numeric|min:0|required_without:entries.*.prixVenteTTC,entries.*.TVA_vente",
            'entries.*.prixVenteTTC' =>"numeric|min:0|required_without:entries.*.prixVenteHT,entries.*.TVA_vente|greater_than_or_equal_field:entries.*.prixVenteHT",
            
            'entries.*.reduction' => "numeric|min:0",
            'entries.*.reductionHT' => 'boolean|required_with:entries.*.reduction',
            'entries.*.reductionParPourcentage' => 'boolean|required_with:entries.*.reduction',
            'entries.*.quantite'=>"numeric|min:0|greater_than_or_equal_field:entries.*.quantiteLivree",
            'entries.*.quantiteLivree'=>"numeric|min:0",
            'entries.*.produit_id'=> [function ($attribute, $value, $fail) {
                if (! \App\Produit::find($value)) {
                    $fail(':attribute is an invalid Produit id !');
                }
            }],
            'entries.*'=> [function ($attribute, $value, $fail) {
               if(array_get($value,'reduction') !== null)
               {
                   DocumentEntry::compute_price_HT_TVA_TTC($value);
                   $line = new DocumentEntry($value);
                   if($line->prixVenteHT_apresReduction < 0)
                   {
                        $fail('Invalid reduction');   
                   }
               }
            }],
            'entries.*.enrgNouveauProduit' => 'boolean',           
            'destNom'=> 'required',
            'destEmail' => 'email',
            'destAssujetiTVA' => 'boolean|required',
            'destId' => [
                function ($attribute, $value, $fail) {
                    if (! \App\Contact::find($value)) {
                        $fail(':attribute is an invalid Contact id !');
                    }
                }
            ],
            'enrgNouveauClient' => 'boolean',
            'reduction' => 'numeric|min:0',
            'reductionHT' => 'boolean|required_with:reduction',
            'reductionParPourcentage' => 'boolean|required_with:reduction',
           
        ]);
        //one last validation rule, so complex I couldn't include in the validate method
        // it validates that the reduction isn't greater than the value of the Facture
        if($request->reduction)
        {
            // we create a Facture instance just for testing
            $potentialFacture = new Facture($request->except('entries'));
            // we then create an array of the document_entries 
            // because we can't save them and use the relationship, so we manually add them to the Facture model
            $document_entries = array();
            foreach ($request->entries as $entry) {
                $document_entries [] = new DocumentEntry(array_add($entry,'quantite',1.00));
            }
            $potentialFacture->document_entries = $document_entries;
            // here we compare the value of the reduction with the value of the Facture
            $validated = true;
            if($potentialFacture->reductionHT)
            {
                if($potentialFacture->prixHT < $potentialFacture->montantReduction)
                {
                    $validated = false;
                }
            }else
            {
                if($potentialFacture->prixTTC < $potentialFacture->montantReduction)
                {
                    $validated = false;
                }
            }

            if(! $validated)
            {
                return response()->json([
                    'message' => 'La réduction est supérieur au montant de la facture',    
                    ],422);
            }
        }
        
        





        $fact = new Facture($request->except('entries'));
        $fact->addedBy = Auth::user()->id;
        
        $result = $fact->save();
       
        // Processing document entries
        foreach($request->entries as $entry)
        { 
            $produit = null;
            if(array_get($entry, "produit_id") != null &&  array_get($entry, "enrgNouveauProduit"))
            {    
                // in this case we save a new Produit
                
                $entry['TVA'] = array_get($entry, 'TVA_vente');
                $produit = new \App\Produit($entry);
                $produit->estVendu = true;
                $produit->addedBy =  Auth::user()->id;
                $produit->save();
                $produit->setFournisseurs([
                        [
                            'prixAchatHT'=> array_get($entry,'prixAchatHT'),
                            'prixAchatTTC'=> array_get($entry,'prixAchatTTC'),
                            'TVA_achat'=> array_get($entry,'TVA_achat')
                        ]
                    ]);
            }
            DocumentEntry::compute_price_HT_TVA_TTC($entry);
            $line = new DocumentEntry($entry);
            if($produit)
            {
                $line->produit_id = $produit->id;
            }
            $results = $result && $fact->document_entries()->save($line);
           
        }
        //to save a new client
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

    /**
     * Set quantite livree of a Facture entry
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setLivraison(Request $request, $id)
    {
        $fact = Facture::find($id);
        if($fact)
        {
            $this->validate($request,[
                'entry_id' => ['required', function ($attribute, $value, $fail) use ($fact) {
                    if( ! \App\DocumentEntry::find($value))
                    {
                        $fail(':attribute is an invalid DocumentEntry id !');   
                    }else if (! $fact->document_entries->contains($value)) {
                        $fail(':attribute does not belong to the Facture !');
                    }
                }],
                'quantiteLivree'=>"required|numeric|min:0"
            ]);

            $entry = \App\DocumentEntry::find($request->entry_id);
            $entry->quantiteLivree = $request->quantiteLivree;
            if( $entry->save() )
            {
                return response()->json(['message'=>'Livraison added'],200);
            }
        }
        return response()->json(['message'=>'Facture not found'],404);
    }
    /**
     * get echeances of this Facture
     * @param int id of Facture
     */
    public function getEcheances(int $id)
    {
        $fact = Facture::find($id);
        if($fact)
        {
            return \App\Http\Resources\EcheanceResources::collection($fact->echeances);
        }
        return response()->json(['message'=>'Facture not found'],404);
    }

    /**
     * set echeances of a Facturee
     * @param Request
     * @param int id of Facture
     */
    public function setEcheances(Request $request, int $id)
    {
        $fact = Facture::find($id);
        if($fact)
        {
            $this->validate($request,[
                'startDate' => 'required|date|after_or_equal:today',
                'divideBy' => 'integer|min:1',
                'each' => 'required_with:devideBy|integer|min:1'
            ]);

            
        }

        return response()->json(['message'=>'Facture not found'],404);

    }
}
