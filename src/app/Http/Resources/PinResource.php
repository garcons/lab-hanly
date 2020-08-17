<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PinResource extends JsonResource
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
            'datetime' => $this->created_at->toIso8601String(),
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}
