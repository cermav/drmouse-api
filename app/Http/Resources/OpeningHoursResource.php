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
            'weekday' => $this->weekday->name,
            'state' => $this->openingHoursState->name,
            'open_at' => $this->open_at,
            'close_at' => $this->close_at
        ];
    }
}
