<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


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
    public function delete()
    { 
        if(Storage::exists($this->chemin))
        {
            Storage::delete($this->chemin);
        }        
        parent::delete();
    }

}
