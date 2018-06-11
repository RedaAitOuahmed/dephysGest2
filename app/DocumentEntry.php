<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentEntry extends Model
{
    protected $fillable=['nom','TVA_vente','prixVenteHT','TVA_Achat','prixAchatHT','quantiteLivree','reduction','reductionHT','reductionParPourcentage','unite','produit_id','details'];
    public function document()
    {
        return $this->morphTo();
    }
}
