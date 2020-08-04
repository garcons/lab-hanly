<?php

namespace App\Http\Controllers\Api;

use App\Eloquents\Friend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MeController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        // Tokenから自分のIDを取得
        $myId = $request->user()->id;

        // EloquentからPin情報含めてデータ取得
        $myInfo = Friend::with(['pin'])->find($myId);

        // とりあえず、そのままレスポンスします（後ほど整形します）
        return response()->json($myInfo);
    }
}
