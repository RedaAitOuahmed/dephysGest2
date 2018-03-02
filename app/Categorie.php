<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    /**
     * this model defines a way to group Products
     */
    public function produits()
    {
        return $this->hasMany('App\Produit');
    }

    public function addedBy()
    {
        return $this->belongsTo('App\User','addedBy');
    }
}
