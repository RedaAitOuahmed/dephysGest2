<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FactureFinale extends Model
{
    /**
     * still not sure abt this one 
     */
    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
    }
    public function paiements()
    {
        return $this->morphMany('App\Document', 'payable');
    }
    public function finalizedBy()
    {
        return $this->brlongsTo('App\User','finalizedBy');
    }
}
