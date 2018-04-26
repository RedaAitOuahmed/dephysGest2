<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fichier extends Model
{
    protected $fillable = ['nom','tache_id'];

    public function tache()
    {
        return $this->belongsTo('App\Tache');
    }

    public function addedBy()
    {
       return $this->belongsTo('App\User','addedBy'); 
    }

}
