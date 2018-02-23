<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    
    

    protected $fillable = ['nom','relation','tel','email','adresse','fax'];
   

    public function contactable()
    {
        return $this->morphTo();
    }

    public function produits()
    {
        return $this->hasMany('App\Produits','fournisseurs_produits','id','id');
    }

    public function added_by()
    {
        return $this->belongsTo('App\User','added_by');
    }
    

    public function dummyDisplay()
    {
             echo "Imma contact";
    }


    
}
