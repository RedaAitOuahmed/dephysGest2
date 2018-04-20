<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Relation extends Model
{
    public function contact()
    {
        return $this->belongsTo('App\Contact');
    }
}
