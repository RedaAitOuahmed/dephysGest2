<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    protected $fillable = ['destNom','destEmail','destAdd','destTel','destId','destAssujetiTVA',
    'destNumTVA', 'reduction','reductionHT','reductionParPourcentage', 'basDePage'];
    public function paiements()
    {
        return $this->morphMany('App\Paiement', 'document');
    }
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
    public function getDocumentEntries()
    {
        $array = array();
        foreach($this->document_entries as $documentEntry)
        {
            $array[] = (new \App\Http\Resources\DocumentEntryResource($documentEntry))->resource;
        }
        return $array;
    }

    public function getPrice()
    {
        $somme = 0;
        if($this->destAssujetiTVA)
        {
            foreach($this->document_entries as $product)
            {
            $somme += $product->prixHT + ($product->prixVenteHT *  $product->TVA_vente );
            }
        }else
        {
            foreach($this->document_entries as $product)
            {
                $somme += $product->prixHT;
            }
            
        }

        return $somme;
        
    }
}
