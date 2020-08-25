<?php

namespace App\Http\Controllers\Api;

use App\Eloquents\Friend;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ImageStoreRequest;

class ImageController extends Controller
{
    protected $friend;

    public function __construct(Friend $friend)
    {
        $this->friend = $friend;
    }

    /**
     * @param \App\Http\Requests\Api\ImageStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ImageStoreRequest $request)
    {
        $myId = \DB::transaction(function () use ($request) {
            $myId = $request->user()->id;

            $savedPath = $request->file->store('images', 'local');

            try {
                $this->friend->imageStore($myId, $savedPath);
            } catch (\Exception $e) {
                // DBでのエラーが起きた場合は、保存したファイルを削除
                \Storage::disk('local')->delete($savedPath);
                throw $e;
            }

            return $myId;
        });

        return response()->json([
            'image_url' => route('web.image.get', ['friendId' => $myId])
        ]);
    }
}
