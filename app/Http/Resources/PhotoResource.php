<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PhotoResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {

        // add image size if file exists
        if (file_exists( Storage::disk('public')->path($this->path) ) ) {
            list($width, $height) =  getimagesize( Storage::disk('public')->path($this->path));

            return [
                'id' => $this->id,
                'path' => Storage::disk('public')->url($this->path),
                'width' => $width,
                'height' => $height,
                'position' => $this->position
            ];
        }

        return [
            'id' => $this->id,
            'path' => Storage::disk('public')->url($this->path),
            'position' => $this->position
        ];


    }

}
