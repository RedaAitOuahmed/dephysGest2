<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class DocumentEntryResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        
        $typeReduction = $this->reductionParPourcentage ? "%" :"€";
        $reductionSur = $this->reductionHT? "HT" : "TTC";
        return[
            "id" => $this->id,
            "nom" => $this->nom,
            "TVA_vente" => $this->TVA_vente,
            "prixAchatHT" => $this->prixAchatHT,
            "prixAchatTTC" => $this->prixAchatTTC,
            "prixVenteHT" => $this->prixVenteHT,
            "prixVenteTTC" => $this->prixVenteTTC,
            "reduction" => $this->reduction,
            "typeReduction" => $this->when($this->reductionParPourcentage !== null, function () use ($typeReduction) {
                return $typeReduction;
            }),
            "reductionSur" => $this->when($this->reductionHT !== null, function () use ($reductionSur) {
                return $reductionSur;
            }),
            "prixVenteHT_apresReduction"=> $this->prixVenteHT_apresReduction,
            "prixVenteTTC_apresReduction"=> $this->prixVenteTTC_apresReduction,
            "quantite" => $this->quantite,
            "unite" =>$this->unite,
            "quantiteLivree" => $this->quantiteLivree,
            "produit_id" => $this->produit_id,
            "details" => $this->details

        ];
    }
}
