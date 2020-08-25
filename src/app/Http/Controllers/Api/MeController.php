<?php

namespace App\Http\Controllers\Api;

use App\Eloquents\Friend;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class MeController extends Controller
{
    protected $friend;

    public function __construct(Friend $friend)
    {
        $this->friend = $friend;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\FriendResource
     */
    public function me(Request $request)
    {
        $myId = $request->user()->id;

        $myInfo = $this->friend->findById($myId);
        if (!$myInfo) {
            // meという自分の情報にアクセスしてデータが無いのは異常すぎるので500エラーにしてます。
            abort(500);
        }

        return new \App\Http\Resources\FriendResource($myInfo);
    }
}
