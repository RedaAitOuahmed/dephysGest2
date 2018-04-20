<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PersonneResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

     /**
      * if it's just a Person returns this resource
      * but if it's a user returns the user Ressource
      */
    public function toArray($request)
    {  
        return new ContactResource($this->contacts()->first());
        // if($this->user()->first() == null)
        // {
        //     return [
        //         'type'=> 'Personne',
        //         'nom' => $this->nom,
        //         'relation' => $this->when($this->relation != null, $this->relation),
        //         'prenom' => $this->when($this->prenom != null, $this->prenom),
        //         'email'=> $this->when($this->email != null, $this->email),
        //         'tel'=> $this->when($this->tel != null, $this->tel),  
        //         'adresse'=> $this->when($this->adresse != null, $this->adresse),  
        //         'fax'=> $this->when($this->fax != null, $this->fax),    
        //         'addedBy'=> $this->when($this->addedBy()->first() != null, new UserResource($this->addedBy()->first())),
    
        //     ]; 
        // }
        
        // return new UserResource($this->user()->first());
    }
}
