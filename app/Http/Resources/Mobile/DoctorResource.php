<?php

namespace App\Http\Resources\Mobile;

use App\Http\Resources\OpeningHoursResource;
use App\Http\Resources\PhotoResource;
use App\Http\Resources\ServiceResource;
use App\ScoreItem;
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
        $openingHours = $this->user->openingHours->pluck('id');
        $all_properties = $this->user->properties()->where('is_approved', 1)->get();
        $services = $this->user->services()->where('is_approved', 1)->get();
        $photos = $this->user->photos->pluck('path');

        // split properties
        $properties = [];
        foreach ($all_properties as $item) {
            $properties[$item->property_category_id][] = $item->id;
        }

        return [

            'id' => $this->user->id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'avatar' => $this->user->avatar,

            'search_name' => $this->search_name,
            'description' => $this->description,
            'speaks_english' => $this->speaks_english,
            'completeness' => $this->profile_completedness,

            'address' => [
                'street' => $this->street,
                'city' => $this->city,
                'country' => $this->country,
                'post_code' => $this->post_code,
                'website' => $this->website,
                'phone' => $this->phone,
                'second_phone' => $this->second_phone,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ],

            'opening_hours' => $openingHours,

            'staff_info' => [
                'doctors_count' => $this->working_doctors_count,
                'doctors_names' => nl2br( $this->working_doctors_names ),
                'nurses_count' => $this->nurses_count,
                'others_count' => $this->other_workers_count,
            ],

            'services' => $services,

            'gallery' => $photos,

            'properties' => [
                'equipment' => array_key_exists(1, $properties) ? $properties[1] : [],
                'expertise' => array_key_exists(2, $properties) ? $properties[2] : [],
                'specialization' => array_key_exists(3, $properties) ? $properties[3] : []
            ],
        ];
    }
}