<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SignupRequest;
use App\Http\Resources\AccountResource;

class SignupController extends Controller
{
    /**
     * @param \App\Http\Requests\Api\SignupRequest $request
     * @return \App\Http\Resources\AccountResource
     */
    public function signup(SignupRequest $request)
    {
        // こんな風にリクエストデータを受け取ります。
        $email = $request->input('email');
        $password = $request->input('password');
        $nickname = $request->input('nickname');

        // Eloquentを使って、DBに保存します
        $stored = \App\Eloquents\Friend::create([
            'email' => $email,
            'password' => bcrypt($password),
            'nickname' => $nickname,
        ]);

        return new AccountResource($stored);
    }
}
