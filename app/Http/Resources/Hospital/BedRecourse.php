<?php

namespace App\Http\Resources\Hospital;

use Illuminate\Http\Resources\Json\JsonResource;

class BedRecourse extends JsonResource
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
            'name' => $this->name,
            'day_cost' => $this->day_cost,
            'status' => $this->status,
            'current_reservation' =>
                ($this->status == 'Reserved') ? new ReservationRecourse($this->currentReservations()) : null
        ];
    }
}
