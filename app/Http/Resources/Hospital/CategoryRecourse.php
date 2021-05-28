<?php

namespace App\Http\Resources\Hospital;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryRecourse extends JsonResource
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
            'id' => $this->id,
            'cost' => $this->beds
        ];
    }
}
