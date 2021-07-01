<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

class ScoreResource extends JsonResource
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
            'user_id' => $this->user_id,
            'comment' => $this->comment,
            'details' => ScoreDetailResource::collection($this->details),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
