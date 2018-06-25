<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = ['sommePayee'];
    public function addedBy()
    {
        return $this->belongsTo('App\User','addedBy');
    }
    public function document()
    {
        return $this->morphTo();
    }
}
