<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource
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
            'type'=> 'User',
            'nom' => $this->nom,
            'relation' => $this->when($this->relation != null, $this->relation),
            'prenom' => $this->when($this->prenom != null, $this->prenom),
            'email'=> $this->when($this->email != null, $this->email),
            'tel'=> $this->when($this->tel != null, $this->tel),  
            'adresse'=> $this->when($this->adresse != null, $this->adresse),  
            'fax'=> $this->when($this->fax != null, $this->fax),    
        ];
    }
}
