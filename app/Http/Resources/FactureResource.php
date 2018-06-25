<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;


class FactureResource extends Resource
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
        $addedBy_contactId = null;
        $usr = \App\User::find($this->addedBy);
        if($usr)
        {
            $addedBy_contactId = $usr->getContactId();
        }

        return[
            "id"=> $this->id,
            "abandonee" => $this->abandonee,
            "entries" => DocumentEntryResource::collection($this->document_entries),
            "destNom" => $this->destNom,
            "destEmail" => $this->destEmail,
            "destAdd" => $this->destAdd,
            "destId" => $this->destId,
            "destAssujetiTVA" => $this->destAssujetiTVA,
            "destNumTVA" => $this->destNumTVA,
            "prixVenteHT" => $this->prixVenteHT,
            "prixVenteTTC" =>$this->prixVenteTTC,
            "reduction" => $this->reduction,
            "typeReduction" => $this->when($this->reductionParPourcentage !== false, function () use ($typeReduction) {
                return $typeReduction;
            }),
            "reductionSur" => $this->when($this->reductionHT !== false, function () use ($reductionSur) {
                return $reductionSur;
            }),
            "TVA_groupsApresReduction" => $this->TVA_groupsApresReduction,
            "prixVenteHT_apresReduction" => $this->prixVenteHT_apresReduction,
            "prixVenteTTC_apresReduction" =>$this->prixVenteTTC_apresReduction,
            "basDePage" => $this->basDePage,
            'addedBy'=> $addedBy_contactId,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,        

        ];
    }
}
