<?php

namespace App\Http\Resources\Hospital;

use Illuminate\Http\Resources\Json\JsonResource;

class ReservationRecourse extends JsonResource
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
            'user' => new UserResource($this->user),
            'start_at' => $this->start_at,
            'end_at' => $this->end_at
        ];
    }
}
