<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Produit extends Model
{
    protected $fillable = ['nom','codeBarre','prixVenteHT',
    'TVA', 'prixVenteTTC', 'estAchete', 'estVendu' ,'description', 'categorie_id'];
    public function categorie()
    {
        return $this->belongsTo('App\Categorie');
    }

    public function addedBy()
    {
        return $this->belongsTo('App\User','addedBy');
    }
    public function fournisseurs()
    {
        return $this->belongsToMany('App\Contact','fournisseurs_produits','produit_id','fournisseur_id')->withPivot('prixAchatHT','prixAchatTTC','TVA_achat');
    }

    public function documents()
    {
        return $this->belongsToMany('App\Document');
    }

    public function getFournisseurs()
    {
        $toReturn = array();        
        foreach($this->fournisseurs as $fournisseur)
        {         
            $toReturn[] = $fournisseur->pivot;
        }
        return $toReturn;
    }
    /**
     * override parent delete function
     * first deletes pivot table rows concerning this record before delting it
     * @return boolean 
     */


    public function delete()
    {
        if($this->forunisseurs)
        {
            $this->forunisseurs()->detach();
        }
        return parrent::delete();
    }


    /**
     * Removes all previous fournisseurs
     * @param fournisseurs : array of objects containing either id and prixAchat or nom and prixAchat or just prixAchat
     * in the first case we just attach the contact (fournisseur) to this Produit, we also add the fournisseur relation to the contact if it didn't exist before 
     * in the second case, we create a contact (fournisseur) and we add a personal task for the user to complete the info.
     * finnaly if there is only prixAchat we attach it to a default fournisseur for all produits with unknown fournisseur  
     */

    public function setFournisseurs(array $fournisseurs)
    {
       
        if($this->forunisseurs)
        {
            $this->forunisseurs()->detach();
        }
       
      
        foreach ($fournisseurs as $fournisseur) {
            $pivotAttributes = [
                'prixAchatHT'=>array_get($fournisseur, "prixAchatHT", null),
                'TVA_achat'=>array_get($fournisseur, "TVA_achat",null),
                'prixAchatTTC'=>array_get($fournisseur, "prixAchatTTC", null)
            ];
            //In case the user has set a Fournisseur that is already in the database
            if(array_key_exists("id",$fournisseur))
            {
                $contact = \App\Contact::find($fournisseur["id"]);
                if($contact)
                {
                    //If the contact isn't set as a Fournisseur, then a "Fournisseur" relation will be added automatically to the contact
                    if(! \in_array('fournisseur',$contact->getRelations()))
                    {
                        $rel = new \App\Relation();
                        $rel->relation = "fournisseur";
                        $contact->relations()->save($rel);
                    }
                    $this->fournisseurs()->attach($contact->id, $pivotAttributes);
                }
                
            }else if( array_key_exists("nom",$fournisseur))
            //Else In case the user has gave a "nom" of a Fournisseur that dosen't exist in the database
            //So we create a new Contact with this name and save it to the database            
            {
                //we create a new contact
                $contact =  new \App\Contact(['nom'=>$fournisseur["nom"]]);
                $contact->addedBy = Auth::user()->id;
                $contact->save();
                $contact->set_relations(['fournisseur']);
                // we attach this produit to the new created contact (fournisseur)
                $this->fournisseurs()->attach($contact->id, $pivotAttributes);
                //we add a tache to complete the info abt the newly added contact
                $tache = new \App\Tache(
                    ['nom'=>"compléter la fiche d'information de $contact->nom",
                    'visibleAuxAutres' => false,
                    'etat' => 'aFaire',
                    'description' => "cette tâche a été générée automatiquement après l'ajout d'un produit de ce fournisseur"
                ]);                
                $tache->addedBy = Auth::user()->id;
                $tache->save();
                $tache->setAssignations([Auth::user()->id]);
            }else if(
                array_key_exists("prixAchatHT",$fournisseur) ||
                array_key_exists("prixAchatTTC",$fournisseur)||
                array_key_exists("TVA_achat",$fournisseur))
            // In case the user didn't set any Fournisseur, we then set this Produit to a default Fournisseur
            {
                //fournisseur par défaut is a default Fournisseur created automatically by the DefaultRecordsSeeder
                $defaultFourn= \App\Contact::where('nom','=','fournisseur par défaut')->whereNotNull('created_at')->orderBy('created_at', 'asc')->first();
                $this->fournisseurs()->attach($defaultFourn->id, $pivotAttributes);
            }
        }
    }
    /**
     * @param double prixHT
     * @param double prixTTC
     * @param double TVA
     * based on two of these arguments the function calculates prixHT and TVA that should be 
     * @return StdClass an object containing the prixHT and TVA
     * @return NULL if only one argument is non null
     */

    public static function getPrice_HT_TVA($prixHT = null, $prixTTC = null, $TVA = null)
    {
        $obj = new \StdClass();
        if($prixHT && $TVA)
        {
            $obj->prixHT = $prixHT;
            $obj->TVA = $TVA;
            return $obj;
        }
        if($prixTTC && $prixHT)
        {
            $obj->prixHT = $prixHT;
            $obj->TVA = ($prixTTC-$prixHT)/$prixHT * 100; 
            return $obj;  
        }
        if($prixTTC && $TVA)
        {
            $obj->prixHT = $prixTTC / ($TVA/100 + 1);
            $obj->TVA = $TVA;
            return $obj;
        }
        return null;
    }
}
