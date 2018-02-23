<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FactureFinale extends Model
{
    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
    }
    public function paiements()
    {
        return $this->morphMany('App\Document', 'payable');
    }
    public function finalized_by()
    {
        return $this->brlongsTo('App\User','finalized_by');
    }
}
