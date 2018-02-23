<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
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
