<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FriendCollection extends ResourceCollection
{
    // 単数形を自動で取得する（リソースファイルがFriend.phpなら自動で取得する）が、
    // Eloquentと名前がかぶるのであえて名前を変えているため、ここで指定してあげる
    public $collects = 'App\Http\Resources\FriendResource';

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
