<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    
    
    public $timestamps = false;
    protected $fillable = ['nom','relation','tel','email','adresse','fax'];
   

    public function contactable()
    {
        return $this->morphTo();
    }

    public function produits()
    {
        return $this->hasMany('App\Produits','fournisseurs_produits','id','id');
    }

    public function dummyDisplay()
    {
             echo "Imma contact";
    }


    
}
