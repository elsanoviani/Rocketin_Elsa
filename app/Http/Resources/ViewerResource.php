<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ViewerResource extends JsonResource
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
            "name" => $this->name,
            "id_movie" => $this->id_movie,
            "qty" => $this->qty,
            "title"=> $this->title,
            "description"=> $this->description,
            "duration"=> $this->duration,
            "genres"=> $this->genres,
            "artists"=> $this->artists
        ];
    }
}
