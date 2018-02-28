<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    /**
     * a document can be either a BonDeCommande or a FactureBrouillon or a Devis
     */
    public function documentable()
    {
        return $this->morphTo();
    }
    
    public function produits()
    {
        return $this->hasMany('\App\Produit');
    }

    public function added_by()
    {
       return $this->belongsTo('App\User','added_by'); 
    }

    
}
