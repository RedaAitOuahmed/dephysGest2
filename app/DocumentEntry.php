<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentEntry extends Model
{
    protected $fillable=['nom','TVA','prixAchat','prixVente','quantiteLivree','produit_id','details'];
    public function document()
    {
        return $this->morphTo();
    }
}
