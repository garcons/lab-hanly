<?php

namespace App\Http\Controllers\Api;

use App\Eloquents\Friend;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SignupRequest;
use App\Http\Resources\AccountResource;

class SignupController extends Controller
{
    protected $friend;

    public function __construct(Friend $friend)
    {
        $this->friend = $friend;
    }

    /**
     * @param \App\Http\Requests\Api\SignupRequest $request
     * @return \App\Http\Resources\AccountResource
     */
    public function signup(SignupRequest $request)
    {
        $stored = $this->friend->store(
            $request->input('email'),
            $request->input('password'),
            $request->input('nickname')
        );

        return new AccountResource($stored);
    }
}
