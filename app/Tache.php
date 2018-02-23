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

    public function created_by()
    {
       return $this->belongsTo('App\User');  // will check for a created_by_id
    }

    public function assigned_to()
    {
       return $this->belongsToMany('App\User','taches_users','id','id'); 
    }
    
}
