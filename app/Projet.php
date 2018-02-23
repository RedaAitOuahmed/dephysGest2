<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    public function taches()
    {
        return $this->hasMany('App\Tache');
    }

    public function fichiers()
    {
        return $this->hasManyThrough('App\Fichier', 'App\Tache');
    }

    public function added_by()
    {
       return $this->belongsTo('App\User','added_by'); 
    }

   
}

