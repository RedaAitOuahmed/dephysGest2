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

    protected $fillable = ['nom','relation','tel','email','adresse','fax','addedBy'];
   

    public function contactable()
    {
        return $this->morphTo();
    }

    public function produits()
    {
        return $this->hasMany('App\Produits','fournisseurs_produits','id','id');
    }

    public function addedBy()
    {
        return $this->belongsTo('App\User','addedBy');
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


    
}
