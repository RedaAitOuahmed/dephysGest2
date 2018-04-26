<?php

namespace App\Http\Resources;
use File;
use Illuminate\Http\Resources\Json\Resource;

class FichierResource extends Resource
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
        return 
        [
            'id' => $this->id,
            'nom' => $this->nom,
            'taille' => File::size(\storage_path('app/'.$this->chemin)),
            'addedBy' => $addedBy_contactId,
            'tache_id' => $this->tache_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
