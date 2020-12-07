<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OpeningHoursResource extends JsonResource
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
            'weekday_id' => $this->weekday->id,
            'weekday' => $this->weekday->name,
            'state_id' => $this->opening_Hours_State->id,
            'state' => $this->opening_Hours_State->name,
            'open_at' => $this->open_at,
            'close_at' => $this->close_at,
        ];
    }
}
