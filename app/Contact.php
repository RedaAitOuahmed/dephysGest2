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


    
}
