<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fichier extends Model
{
    public function tache()
    {
        return $this->belongsTo('App\Tache');
    }

    public function added_by()
    {
       return $this->belongsTo('App\User');  // will check for a created_by_id
    }

}
