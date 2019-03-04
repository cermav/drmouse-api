<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $openingHours = $this->user->openingHours;
        $properties = $this->user->properties()->where('is_approved', 1)->get();
        $services = $this->user->services()->where('is_approved', 1)->get();
        $photos = $this->user->photos;
        return [
            'id' => $this->user->id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'avatar' => $this->user->avatar,
            'created_at' => $this->user->created_at,
            'updated_at' => $this->user->updated_at,
            
            'search_name' => $this->search_name,
            'description' => $this->description,
            'speaks_english' => $this->speaks_english,
            'phone' => $this->phone,
            'second_phone' => $this->second_phone,
            'website' => $this->website,
            'street' => $this->street,
            'city' => $this->city,
            'country' => $this->country,
            'post_code' => $this->post_code,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'working_doctors_count' => $this->working_doctors_count,
            'working_doctors_names' => $this->working_doctors_names,
            'nurses_count' => $this->nurses_count,
            'other_workers_count' => $this->other_workers_count,
            
            'openingHours' => OpeningHoursResource::collection($openingHours),
            'properties' => PropertyResource::collection($properties),
            'services' => ServiceResource::collection($services),
            'gallery' => PhotoResource::collection($photos)
        ];
    }
}
