<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ProduitResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $addedBy_contactId = null;
        $usr = \App\User::find($this->addedBy);
        if($usr)
        {
            $addedBy_contactId = $usr->getContactId();
        }
        return [
            "id" => $this->id,
            "nom" => $this->nom,
            "codeBarre" => $this->codeBarre,
            "prixAchat" => $this->prixAchat,
            "prixVente" => $this->prixVente,
            "TVA" => $this->TVA,
            "addedBy" => $addedBy_contactId,
            "estAchete" => $this->estAchete,
            "estVendu" => $this->estVendu,
            "description" => $this->description,
            "categorie_id" => $this->categorie_id,
            "fournisseurs" => $this->getFournisseurs(),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }
}
