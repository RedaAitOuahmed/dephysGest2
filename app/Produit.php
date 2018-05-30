<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Produit extends Model
{
    protected $fillable = ['nom','codeBarre','prixVente',
    'TVA', 'estAchete', 'estVendu' ,'description', 'categorie_id'];
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
        return $this->belongsToMany('App\Contact','fournisseurs_produits','produit_id','fournisseur_id');
    }

    public function documents()
    {
        return $this->belongsToMany('App\Document');
    }

    public function getFournisseurs()
    {
        $toReturn = array();        
        $fournisseurs = $this->fournisseurs;
        foreach($fournisseurs as $fournisseur)
        {
            $toReturn [] = array("id"=>$fournisseur->id, "prixAchat"=>$fournisseur->pivot->prixAchat);
        }
        return $toReturn;
    }


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
            if(\array_key_exists("id",$fournisseur))
            {
                $contact = \App\Contact::find($fournisseur["id"]);
                if($contact)
                {
                    if(! \in_array('fournisseur',$contact->getRelations()))
                    {
                        $rel = new \App\Relation();
                        $rel->relation = "fournisseur";
                        $contact->relations()->save($rel);
                    }
                    $this->fournisseurs()->attach($contact->id, ['prixAchat'=>$fournisseur["prixAchat"]]);
                }
                
            }else if( \array_key_exists("nom",$fournisseur))
            {
                //we create a new contact
                $contact =  new \App\Contact(['nom'=>$fournisseur["nom"]]);
                $contact->addedBy = Auth::user()->id;
                $contact->save();
                $contact->set_relations(['fournisseur']);
                // we attach this produit to the new created contact (fournisseur)
                $this->fournisseurs()->attach($contact->id, ['prixAchat'=>$fournisseur["prixAchat"]]);
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
            }else if(\array_key_exists("prixAchat",$fournisseur))
            {
                // fournisseur par défaut is a default Fournisseur created automatically by the DefaultRecordsSeeder
                $defaultFourn= \App\Contact::where('nom','=','fournisseur par défaut')->whereNotNull('created_at')->orderBy('created_at', 'asc')->first();
                $this->fournisseurs()->attach($defaultFourn->id, ['prixAchat'=>$fournisseur["prixAchat"]]);
            }
        }
    }
}
