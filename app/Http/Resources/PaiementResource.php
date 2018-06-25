<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PaiementResource extends Resource
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
            'sommePayee' => $this->sommePayee,
            'addedBy'=> $addedBy_contactId,
            'document_id'=> $this->document_id,
            'document_type' => $this->document_type,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
        ];
    }
}
