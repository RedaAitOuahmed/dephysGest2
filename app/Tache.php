<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tache extends Model
{
    public function projet()
    {
        return $this->belongsTo('App\Projet');
    }

    public function fichiers()
    {
        return $this->hasMany('App\Fichier');
    }

    public function addedBy()
    {
       return $this->belongsTo('App\User','addedBy'); 
    }

    public function assigned_to()
    {
       return $this->belongsToMany('App\User','taches_users','id','id'); 
    }
    
}
