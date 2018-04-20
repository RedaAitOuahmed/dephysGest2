<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ContactResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

     /**
      * checks if it's a Personne or an Entreprise and returns the resource accordignly
      * returns this resource if it's neither a Personne or an Entreprise but just a Contact
      */
    public function toArray($request)
    {
        $addedBy_contactId = null;
        $usr = \App\User::find($this->addedBy);
        if($usr)
        {
            $addedBy_contactId = $usr->getContactId();
        }
        $baseArray = [
            'id' => $this->id,
            'type' => $this->getType(),
            'nom' => $this->nom,
            'relation' => $this->getRelations(),
            'email'=> $this->when($this->email != null, $this->email),
            'tel'=> $this->when($this->tel != null, $this->tel),  
            'adresse'=> $this->when($this->adresse != null, $this->adresse),  
            'fax'=> $this->when($this->fax != null, $this->fax),  
            'addedBy'=> $addedBy_contactId,
        ];
               
        if( $this->contactable_type == null)
        {
            return $baseArray;
        }
        if($this->contactable_type == 'App\Personne')
        {
            $perData = 
            [   'prenom' => $this->contactable->prenom,
                ];
            return array_merge($baseArray,$perData);
        }
        if($this->contactable_type == 'App\Entreprise')
        {
            $entr = $this->contactable;
            $entrData = [
                'siren' => $entr->siren,
                'siret' => $entr->siret,
                'assujettiTVA' => $entr->assujettiTVA,
                'numTVA' => $entr->numTVA,

            ];
            return array_merge($baseArray,$entrData);
        }else
        {
            return ['message' => 'ERROR : contactable_type unknown'];
        }
    }
}
