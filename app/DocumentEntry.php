<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentEntry extends Model
{
    protected $fillable=['nom','TVA_vente','prixVenteHT','TVA_Achat','prixAchatHT','prixAchatTTC','quantite','quantiteLivree','reduction','reductionHT','reductionParPourcentage','unite','produit_id','details'];
    public function document()
    {
        return $this->morphTo();
    }
    /**
     * @param array entry : represents a document entry, has the following keys :
     * - prixAchatHT
     * - prixAchatTTC
     * - TVA_achat
     * - prixVenteHT
     * - prixVenteTTC
     * - TVA_vente
     * 
     * This function edits the array so that the data presented by the previous mentionned array keys is consistent.
     * 
     */

    public static function compute_price_HT_TVA_TTC(array &$entry)
    {
        //Vente
        $HT = array_get($entry,'prixVenteHT');
        $TTC = array_get($entry,'prixVenteTTC');
        $TVA = array_get($entry,'TVA_vente');
        $price = \App\DocumentEntry::getPrice_HT_TVA_TTC($HT,$TTC,$TVA);
        if($price)
        {
            $entry['prixVenteHT'] = $price->prixHT;
            $entry['prixVenteTTC'] = $price->prixTTC;
            $entry['TVA_vente'] = $price->TVA;
        }
        //Achat
        $HT = array_get($entry,'prixAchatHT');
        $TTC = array_get($entry,'prixAchatTTC');
        $TVA = array_get($entry,'TVA_achat');
        $price = \App\DocumentEntry::getPrice_HT_TVA_TTC($HT,$TTC,$TVA);
        if($price)
        {
            $entry['prixAchatHT'] = $price->prixHT;
            $entry['prixAchatTTC'] = $price->prixTTC;
            $entry['TVA_achat'] = $price->TVA;
        }
    }

    /**
     * @param double prixHT
     * @param double prixTTC
     * @param double TVA
     * based on two of these arguments the function calculates prixHT, prixTTC and TVA that should be 
     * @return StdClass an object containing the prixHT, TVA and prixTTC
     * @return null if two or more parameters are Null
     */

    public static function getPrice_HT_TVA_TTC($prixHT, $prixTTC, $TVA)
    {
        $obj = new \StdClass();
        if($prixHT && $TVA)
        {
            $obj->prixHT = $prixHT;
            $obj->TVA = $TVA;
            $obj->prixTTC = (1+($TVA/100)) * $prixHT;
            return $obj;
        }
        if($prixTTC && $prixHT)
        {
            $obj->prixHT = $prixHT;
            $obj->TVA = ($prixTTC-$prixHT)/$prixHT * 100; 
            $obj->prixTTC = $prixTTC;
            return $obj;  
        }
        if($prixTTC && $TVA)
        {
            $obj->prixHT = $prixTTC / ($TVA/100 + 1);
            $obj->TVA = $TVA;
            $obj->prixTTC = $prixTTC;
            return $obj;
        }
        return null;
    }

    public function getPrixVenteTTCAttribute()
    {
        return (1+($this->TVA_vente/100)) * $this->prixVenteHT;
    }
    
    public function getPrixVenteHTApresReductionAttribute()
    {
        if($this->reduction > 0)
        {
            if($this->reductionHT)
            {
                if($this->reductionParPourcentage)
                {
                    return (1-$this->reduction/100) * $this->prixVenteHT; 

                }else
                {
                    return $this->prixVenteHT - $this->reduction;
                }
            }else
            {
                if($this->reductionParPourcentage)
                {
                    $prixTTC_apresReduction = $this->prixVenteTTC * (1 - $this->reduction/100);
                    /*
                    Also we know that : 
                    prixTTC_apresReduction = (1 + this->TVA_vente/100) * prixHT_apresReduction, So:
                    */
                    $prixHT_apresReduction = $prixTTC_apresReduction / (1 + $this->TVA_vente/100);
                    return $prixHT_apresReduction;

                }else
                {
                    $prixTTC_apresReduction = $this->prixVenteTTC - $this->reduction;
                    /*
                    Also we know that : 
                    prixTTC_apresReduction = (1 + this->TVA_vente/100) * prixHT_apresReduction, So:
                    */
                    $prixHT_apresReduction = $prixTTC_apresReduction / (1 + $this->TVA_vente/100);
                    return $prixHT_apresReduction;
                }                
                
            }
        }
        return $this->prixVenteHT;
    }
    public function getPrixVenteTTCApresReductionAttribute()
    {
        return (1+($this->TVA_vente/100)) * $this->prixVenteHT_apresReduction;
    }
}
