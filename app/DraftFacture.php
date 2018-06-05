<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DraftFacture extends Model
{
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
