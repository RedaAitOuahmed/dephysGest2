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

    public function added_by()
    {
        return $this->belongsTo('App\User','added_by');
    }
}
