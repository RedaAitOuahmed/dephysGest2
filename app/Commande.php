<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    public function paiements()
    {
        return $this->morphMany('App\Paiement', 'document');
    }
    public function echeances()
    {
        return $this->morphMany('App\Echeance', 'document');
    }
    public function document_entries()
    {
        return $this->morphMany('App\DocumentEntry', 'document');
    }
    public function addedBy()
    {
        return $this->belongsTo('App\User','addedBy');
    }
}
