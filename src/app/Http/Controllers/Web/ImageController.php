<?php

namespace App\Http\Controllers\Web;

use App\Eloquents\Friend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param int $friendId
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function show(Request $request, int $friendId)
    {
        // 保存したパスの情報をDBから取得
        $path = Friend::find($friendId)->image_path;

        // 取得したパスからファイルレスポンスを生成して返却
        return response()->file(\Storage::disk('local')->path($path));
    }
}
