<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    public function payable()
    {
        return $this->morphTo();
    }
    public function addedBy()
    {
        return $this->belongsTo('App\User','addedBy');
    }
    
}
