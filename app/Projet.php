<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    protected $fillable = ['nom','description'];
    public function taches()
    {
        return $this->hasMany('App\Tache');
    }

    public function fichiers()
    {
        return $this->hasManyThrough('App\Fichier', 'App\Tache');
    }

    public function addedBy()
    {
       return $this->belongsTo('App\User','addedBy'); 
    }

   
}

