<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class EntrepriseResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type'=> 'Entreprise',
            'nom' => $this->nom,
            'relation' => $this->when($this->relation != null, $this->relation),
            'siren' => $this->when($this->siren != null, $this->siren),
            'siret' => $this->when($this->siret != null, $this->siret),
            'assujettiTVA' => $this->when($this->assujettiTVA != null, $this->assujettiTVA),
            'numTVA' => $this->when($this->numTVA != null, $this->numTVA),
            'email'=> $this->when($this->email != null, $this->email),
            'tel'=> $this->when($this->tel != null, $this->tel),  
            'adresse'=> $this->when($this->adresse != null, $this->adresse),  
            'fax'=> $this->when($this->fax != null, $this->fax),  
            'addedBy'=> $this->when($this->addedBy()->first() != null, new UserResource($this->addedBy()->first())),

        ];
    }
}
