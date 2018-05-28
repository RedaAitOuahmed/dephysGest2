<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class TacheResource extends Resource
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
            'id' => $this->id,
            'nom' => $this->nom,
            'description' => $this->description,
            'visibleAuxAutres' => $this->visibleAuxAutres,
            'dateLimite' => $this->dateLimite,
            'etat' => $this->etat,
            'projetId' => $this->projet->id,
            'addedBy' => $addedBy_contactId,
            'assignedTo' => $this->getAssignations(),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
       
    }
}
