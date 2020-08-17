<?php

namespace App\Http\Controllers\Api;

use App\Eloquents\Friend;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ImageStoreRequest;

class ImageController extends Controller
{
    /**
     * @param \App\Http\Requests\Api\ImageStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ImageStoreRequest $request)
    {
        $myId = \DB::transaction(function () use ($request) {
            // Tokenから自分のIDを取得
            $myId = $request->user()->id;

            // これだけでimagesディレクトリにローカル（非公開で）保存
            // 保存場所は、storage/app/images/　の下
            $savedPath = $request->file->store('images', 'local');

            // 後はDBにパスを保存しておく
            Friend::find($myId)
                ->fill([
                    'image_path' => $savedPath,
                ])
                ->save();

            return $myId;
        });

        // 取得用のURLを設定してレスポンス(routes/web.phpのnameを基にURL生成)
        return response()->json([
            'image_url' => route('web.image.get', ['userId' => $myId])
        ]);
    }
}
