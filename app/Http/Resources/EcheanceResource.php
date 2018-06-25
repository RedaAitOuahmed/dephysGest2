<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class EcheanceResource extends Resource
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
            "id" => $this->id,
            "sommePayee" => $this->sommePayee,
            "sommeRestante" => $this->sommeRestante,
            "dueDate" => $this->dueDate,
            'document_id'=> $this->document_id,
            'document_type' => $this->document_type,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
        ];
    }
}
