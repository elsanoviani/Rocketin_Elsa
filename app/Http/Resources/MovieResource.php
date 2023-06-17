<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
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
            "title" => $this->title,
            "description" => $this->description,
            "duration" => $this->duration,
            "artists" => $this->artists,
            "genres" => $this->genres,
            "watch_url" => $this->watch_url
        ];
    }
}
