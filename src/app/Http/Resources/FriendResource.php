<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FriendResource extends JsonResource
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
            'nickname' => $this->nickname,
            'email' => $this->email,
            'image_url' => $this->image_path
                ? route('web.image.get', [
                    'friendId' => $this->id,
                    't' => $this->updated_at->getTimestamp() // キャッシュ対策
                ])
                : null,
            'pin' => PinResource::make($this->whenLoaded('pin')),
        ];
    }
}
