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
        $all_properties = $this->user->properties()->where('is_approved', 1)->get();
        $services = $this->user->services()->where('is_approved', 1)->get();
        $photos = $this->user->photos;

        // split properties
        $properties = [];
        foreach ($all_properties as $item) {
            $properties[$item->property_category_id][] = (object) ['id' => $item->id, 'name' => $item->name];
        }

        return [

            'id' => $this->user->id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'avatar' => $this->user->avatar,

            'search_name' => $this->search_name,
            'description' => $this->description,
            'slug' => $this->slug,
            'speaks_english' => $this->speaks_english,
            'completeness' => $this->profile_completedness,

            'total_score' => $this->total_score,
            'open' => $this->open,

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

            'opening_hours' => OpeningHoursResource::collection($openingHours),

            'staff_info' => [
                'doctors_count' => $this->working_doctors_count,
                'doctors_names' => $this->working_doctors_names,
                'nurses_count' => $this->nurses_count,
                'others_count' => $this->other_workers_count,
            ],

            'services' => ServiceResource::collection($services),

            'gallery' => PhotoResource::collection($photos),

            'properties' => [
                'equipment' => array_key_exists(1, $properties) ? $properties[1] : [],
                'expertise' => array_key_exists(2, $properties) ? $properties[2] : [],
                'specialization' => array_key_exists(3, $properties) ? $properties[3] : []
            ],

            'gdpr' => [
                'agreed' => $this->gdpr_agreed,
                'date' => $this->gdpr_agreed_date,
                'ip_address' => $this->gdpr_agreed_ip
            ],

            'created_at' => $this->user->created_at,
            'updated_at' => $this->user->updated_at,
        ];
    }
}
