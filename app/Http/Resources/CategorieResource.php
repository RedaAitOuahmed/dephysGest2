<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class CategorieResource extends Resource
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
          'id'=> $this->id,
          'nom' => $this->nom,
          'description'=>$this->description,
          'addedBy'=> $addedBy_contactId,
          'created_at'=>$this->created_at,
          'updated_at'=>$this->updated_at,
        ];
    }
}
