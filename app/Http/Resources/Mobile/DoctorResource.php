<?php

namespace App\Http\Resources\Mobile;

use App\Http\Resources\OpeningHoursResource;
use App\Http\Resources\PhotoResource;
use App\Http\Resources\ServiceResource;
use App\Models\ScoreItem;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

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
        $all_properties = $this->user
            ->properties()
            ->where('is_approved', 1)
            ->get();
        $services = $this->user
            ->services()
            ->where('is_approved', 1)
            ->get();
        $photos = $this->user->photos->pluck('path');

        // split properties
        /*
        $properties = [];
        foreach ($all_properties as $item) {
            $properties[$item->property_category_id][] = $item->id;
        }*/

        $properties = [];
        foreach ($all_properties as $item) {
            $properties[$item->property_category_id][] = (object) [
                'id' => $item->id,
                'name' => $item->name,
            ];
        }

        /*$scoreQuery = [];
        foreach (ScoreItem::get() as $item) {
            $scoreQuery[] = "(
                SELECT IFNULL( ROUND(((SUM(points) / COUNT(id)) / 5) * 100) , 0) 
                FROM score_details 
                WHERE score_id IN (SELECT id FROM scores WHERE user_id = doctors.user_id)
                    AND score_item_id = {$item->id}
            ) AS total_score ";
        }*/

        // count score
        $score_sum = 0;
        $score_count = 0;
        $score_detail = [];
        foreach (ScoreItem::get() as $item) {
            $variable = 'total_score_' . $item->id;
            $score_detail[$item->id] = $this->$variable;
            $score_sum += $this->$variable;
            $score_count++;
        }

        return [

            'id' => $this->user->id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'avatar' => $this->user->avatar,
            'score' => (object) [
                'total' => $score_sum / $score_count,
                'detail' => $score_detail,
            ],

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
                'doctors_names' => nl2br($this->working_doctors_names),
                'nurses_count' => $this->nurses_count,
                'others_count' => $this->other_workers_count,
            ],

            'services' => $services,

            'gallery' => $photos,

            'properties' => [
                'equipment' => array_key_exists(1, $properties)
                    ? $properties[1]
                    : [],
                'expertise' => array_key_exists(2, $properties)
                    ? $properties[2]
                    : [],
                'specialization' => array_key_exists(3, $properties)
                    ? $properties[3]
                    : [],
                    
            ]
        ];
    }
}
