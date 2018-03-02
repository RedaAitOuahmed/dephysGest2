<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fichier extends Model
{
    public function tache()
    {
        return $this->belongsTo('App\Tache');
    }

    public function addedBy()
    {
       return $this->belongsTo('App\User','addedBy'); 
    }

}
