<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    public function document_entries()
    {
        return $this->morphMany('App\DocumentEntry', 'document');
    }
    public function addedBy()
    {
        return $this->belongsTo('App\User','addedBy');
    }
}
