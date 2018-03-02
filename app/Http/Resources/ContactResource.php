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
               
        if( $this->contactable_type == null)
        {
            return [
                'type' => 'Undefined',
                'nom' => $this->nom,
                'relation' => $this->when($this->relation != null, $this->relation),
                'email'=> $this->when($this->email != null, $this->email),
                'tel'=> $this->when($this->tel != null, $this->tel),  
                'adresse'=> $this->when($this->adresse != null, $this->adresse),  
                'fax'=> $this->when($this->fax != null, $this->fax),  
                'addedBy'=> $this->when($this->addedBy()->first() != null, new UserResource($this->addedBy()->first())),
                // 'contactable_type'=> $this->when($this->contactable_type != null, $this->contactable_type),  
                // 'contactable'=> $this->when($this->contactable()->first() != null, new Personne($this->contactable()->first())),  
            ];

        }
        if($this->contactable_type == 'App\Personne')
        {
            return new PersonneResource($this->contactable);
        }
        if($this->contactable_type == 'App\Entreprise')
        {
            return new EntrepriseResource($this->contactable);
        }else
        {
            return 'ERROR : contactable_type unknown';
        }
    }
}
