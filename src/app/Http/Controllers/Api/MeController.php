<?php

namespace App\Http\Controllers\Api;

use App\Eloquents\Friend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MeController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\FriendResource
     */
    public function me(Request $request)
    {
        // Tokenから自分のIDを取得
        $myId = $request->user()->id;

        // EloquentからPin情報含めてデータ取得
        $myInfo = Friend::with(['pin'])->find($myId);

        return new \App\Http\Resources\FriendResource($myInfo);
    }
}
