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


    /**
     * @return StdClass object containing :
     * - HT : total amount of the Facture HT.
     * - TTC : total amount of the Facture TTC.
     * These two values may be the same if the TVA is null for all products (equal to 0) or if the recepient 
     * of the bill isn't TVA applicable (non assujeti à la tva)
     */

    // public function getAmount()
    // {
    //     $amount = new \StdClass();
    //     $amount->TTC = 0;
    //     $amount->HT = 0;
    //     if($this->destAssujetiTVA)
    //     {
    //         foreach($this->document_entries as $product)
    //         {
    //             $amount->TTC += (1+($product->TVA_vente/100)) * $product->prixVenteHT;
    //             $amount->HT +=  $product->prixVenteHT;
    //         }
    //     }else
    //     {
    //         foreach($this->document_entries as $product)
    //         {
    //             $amount->HT +=  $product->prixVenteHT;
    //         }
    //         $amount->TTC += $amount->HT;
            
    //     }
    //     return $amount;
    // }


    /**
     * get total price HT of the Facture after the discount on each product is applied and before the application of the total discount.
     */

    public function getPrixVenteHTAttribute()
    {
        
       $HT = 0;
       foreach($this->document_entries as $product)
       {
            $HT +=  $product->prixVenteHT_apresReduction * $product->quantite;
       }
       return $HT;
    }
 
     /**
     * get total price TTC of the Facture after the discount on each product is applied and before the application of the total discount.
     */


    public function getPrixVenteTTCAttribute()
    {
        $TTC = 0;

        if($this->destAssujetiTVA)
        {
            foreach($this->document_entries as $product)
            {
                $TTC += (1+($product->TVA_vente/100)) * $product->prixVenteHT_apresReduction * $product->quantite;
            }
            return $TTC;

        }else
        {
            return $this->prixVenteHT;            
        }

    }

     /**
     * get total price HT of the Facture after the discount on each product is applied and after the application of the total discount.
     */
    public function getPrixVenteHTApresReductionAttribute()
    {
        $totalHT = 0;
        foreach ($this->TVA_groupsApresReduction as $TVA_groupApresReduction) {
            $totalHT+= $TVA_groupApresReduction['totalHTApresReduction'];
        }
        return $totalHT;
        
    }
    /**
     * get total price TTC of the Facture after the discount on each product is applied and after the application of the total discount.
     */
    public function getPrixVenteTTCApresReductionAttribute()
    {
        $totalTTC = 0;
        foreach ($this->TVA_groupsApresReduction as $TVA_groupApresReduction) {
            $totalTTC+= $TVA_groupApresReduction['totalHTApresReduction'] + $TVA_groupApresReduction['valeurTVA'];
        }
        return $totalTTC;
        
    }
    /**
     * @return array
     * This function creates an array that contains an associative array for each group of products of this Facture that have the same TVA_vente.
     * This associative array contains :
     * TVA => TVA_vente (taux TVA de vente) of this group of products
     * totalHT => The sum of prixVenteHT_apresReduction of the products contained in the group
     * valeurTVA => the value of the tax that it s gonna be paid
     * If the recepient is not applicable to the taxes, then this function groups all products in one group, and sets the TVA and valeurTVA to 0.
     */

    public function getTVAGroupsAttribute()
    {
        if($this->destAssujetiTVA)
        {
            $globalArray = array();
            $TVAs =$this->document_entries()->distinct('TVA_vente')->pluck('TVA_vente')->all();
            foreach ($TVAs as $TVA) {
                $sommeHT = 0;
                $products = $this->document_entries()->where('TVA_vente','=',$TVA)->get();
                foreach ($products as $product) {
                    $sommeHT += $product->prixVenteHT_apresReduction * $product->quantite;
                }
                $globalArray [] = array('TVA' => $TVA, 'totalHT' => $sommeHT, 'valeurTVA' => $TVA / 100 * $sommeHT);
            }
            return $globalArray;
        }else
        {
            return [
                [
                    'TVA' => 0,
                    'totalHT' => $this->prixVenteHT,
                    'valeurTVA' => 0
                ]
            ];
        }
        
    }
    /**
     * if the discount is by perecentage, it computes the amount of money to be discounted and returns it.
     * the reductionHT Attribute should be checked to know if this value is to be discounted on the HT or on the TTC. 
     */
    public function getMontantReductionAttribute()
    {

        if($this->reductionParPourcentage)
        {
            if($this->reductionHT)
            {
                return $this->reduction/100 * $this->prixVenteHT; 
            }else
            {
                return $this->reduction/100 * $this->prixVenteTTC;
            }
        }
        return $this->reduction;
        
    }

    public function getTVAGroupsApresReductionAttribute()
    {
        $TVA_groups = $this->TVA_groups;
        $montantReduction = $this->montantReduction;
        foreach ($TVA_groups as &$TVA_group) {
            
            if($this->reductionHT)
            {
                if($montantReduction >= $TVA_group['totalHT'])
                {
                    $TVA_group['totalHTApresReduction'] = 0;
                    $TVA_group['valeurTVA'] = 0;
                    // cause totalHTApresReduction is 0
                    $montantReduction -= $TVA_group['totalHT'];

                }else
                {
                    $TVA_group['totalHTApresReduction'] = $TVA_group['totalHT'] - $montantReduction ;
                    $TVA_group['valeurTVA'] = $TVA_group['TVA'] / 100 * $TVA_group['totalHTApresReduction'] ;
                    $montantReduction = 0;                    
                }
            }else
            {
                $groupTTC = $TVA_group['totalHT'] * (1 + $TVA_group['TVA']/100);
                if($montantReduction >= $groupTTC)
                {
                    $TVA_group['totalHTApresReduction'] = 0;
                    $TVA_group['valeurTVA'] = 0;
                    // cause totalHTApresReduction is 0
                    $montantReduction -= $groupTTC;

                }else
                {
                    $groupTTCApresReduction = $groupTTC - $montantReduction;
                    /*
                    Also we know that : 
                        $groupTTCApresReduction = (1 + this->group[TVA]/100) * group[totalHTApresReduction], So:
                    */
                    $TVA_group['totalHTApresReduction']  = $groupTTCApresReduction / (1 + $TVA_group['TVA']/100);
                    $TVA_group['valeurTVA'] = $TVA_group['TVA'] / 100 * $TVA_group['totalHTApresReduction'] ;
                    $montantReduction = 0;
                }
            
            
            }
        }
        return $TVA_groups;   
    }

    
}
