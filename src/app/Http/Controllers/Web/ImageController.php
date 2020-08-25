<?php

namespace App\Http\Controllers\Web;

use App\Eloquents\Friend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    protected $friend;

    public function __construct(Friend $friend)
    {
        $this->friend = $friend;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param int $friendId
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function show(Request $request, int $friendId)
    {
        $path = $this->friend->findById($friendId)->image_path;
        if (!$path) {
            abort(404);
        }

        return response()->file(\Storage::disk('local')->path($path));
    }
}
