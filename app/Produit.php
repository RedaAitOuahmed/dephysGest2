<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    public function categorie()
    {
        return $this->belongsTo('App\Categorie');
    }

    public function added_by()
    {
        return $this->belongsTo('App\User','added_by');
    }
    public function fournisseurs()
    {
        return $this->belongsToMany('App\Contact','fournisseurs_produits','id','id');
    }

    public function documents()
    {
        return $this->belongsToMany('App\Document');
    }
}