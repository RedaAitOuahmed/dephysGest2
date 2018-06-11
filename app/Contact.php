<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
class Contact extends Model
{
    
    /**
     * the generale class of a contact
     * a contact can be : just a contact, a Personne, an Entreprise or a user
     */

    protected $fillable = ['nom','tel','email','adresse','fax'];
   

    public function contactable()
    {
        return $this->morphTo();
    }

    public function produits()
    {
        return $this->hasMany('App\Produits','fournisseurs_produits','fournisseur_id','produit_id')->withPivot('prixAchatHT','prixAchatTTC','TVA_achat');;
    }

    public function addedBy()
    {
        return $this->belongsTo('App\User','addedBy');
    }

    public function relations()
    {
        return $this->hasMany('App\Relation');
    }
    

    public function getAllColumns()
    {
        return Schema::getColumnListing(parent::getTable());
        
    } 
    /**
     * deletes the model from the database if it's not tied to an Entreprise or a Personne
     * else calls the delete method on the Entreprise or Personne model tied to it
     * dosen't do anything if the contact is tied to a user.
     */

    public function delete()
    {
        if($this->contactable!=null)
        {
            $this->contactable->delete();                    
        }else
        {
            parent::delete(); 
        }    
    }
    /**
     * @return Boolean to indicate wether this contact is a  user of the application or not
     */
    public function isUser()
    {
        if($this->contactable==null)
        {
           return false;
        }
        if($this->contactable_type != 'App\Personne')
        {
            return false;
        }
        if(! $this->contactable->isUser())
        {
            return false;
        }
        return true;

    }
    /**
     * @return string the type  of this contact
     */
    public function getType()
    {
        if($this->contactable==null)
        {
           return 'Undefined';
        }
        if($this->contactable_type == 'App\Entreprise')
        {
            return 'Entreprise';
        }
        if($this->isUser())
        {
            return 'User';
        }
        return 'Personne';
    }
    /**
     * @return integer user id if this contact refers to a user
     */
    public function getUserId()
    {
        if($this->isUser())
        {
            return $this->contactable->user_id;
        }
        return null;
    }

    public function getRelations()
    {
        return $this->relations()->distinct()->pluck('relation')->toArray();
    }
    public function set_relations(array $relations)
    {
       $oldRelations = $this->getRelations();

       //first we get rid of relations tha don't exist anymore
       foreach($oldRelations as $oldRelation)
       {
           if( ! \in_array($oldRelation, $relations))
           {
               $this->relations()->where('relation',$oldRelation)->delete();
           }
       }
       // now we add the new relations
       foreach($relations as $newRelation)
       {
            if( ! \in_array($newRelation, $oldRelations))
            {
                $rel = new \App\Relation();
                $rel->relation = $newRelation;
                $this->relations()->save($rel);
            }
       }
    }
  


    
}
