<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Echeance extends Model
{
    public function document()
    {
        return $this->morphTo();
    }
    public function addedBy()
    {
        return $this->belongsTo('App\User','addedBy');
    }
}
