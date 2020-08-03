<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/signup', function (Request $request) {
    return response()->json([
        'id' => 1,
        'nickname' => 'ニックネーム',
        'email' => 'test@example.com',
    ]);
});

Route::get('/me', function (Request $request) {
    return response()->json([
        'id' => 1,
        'nickname' => 'ニックネーム',
        'email' => 'test@example.com',
        'image_url' => null,
        'pin' => [
          'datetime' => '2020-04-19T07:58:20.108Z',
          'latitude' => 33.33333,
          'longitude' => 111.111111,
        ],
    ]);
});

Route::post('/my/image', function (Request $request) {
    return response()->json([
        'image_url' => 'http://localhost/images/1',
    ]);
});

Route::post('/my/pin', function (Request $request) {
    return response()->json([
        [
            'id' => 1,
            'nickname' => 'ニックネーム',
            'email' => 'test@example.com',
            'image_url' => null,
            'pin' => [
                'datetime' => '2020-04-19T07:58:20.108Z',
                'latitude' => 33.33333,
                'longitude' => 111.111111,
            ],
        ],
        [
            'id' => 2,
            'nickname' => 'ニックネーム2',
            'email' => 'test2@example.com',
            'image_url' => null,
            'pin' => null,
        ]
    ]);
});

Route::get('/friends', function (Request $request) {
    return response()->json([
        [
            'id' => 1,
            'nickname' => 'ニックネーム',
            'email' => 'test@example.com',
            'image_url' => null,
            'pin' => [
                'datetime' => '2020-04-19T07:58:20.108Z',
                'latitude' => 33.33333,
                'longitude' => 111.111111,
            ],
        ],
        [
            'id' => 2,
            'nickname' => 'ニックネーム2',
            'email' => 'test2@example.com',
            'image_url' => null,
            'pin' => null,
        ]
    ]);
});

Route::get('/friends/{friendId}', function (Request $request, int $friendId) {
    return response()->json([
        'id' => $friendId,
        'nickname' => 'ニックネーム',
        'email' => 'test@example.com',
        'image_url' => null,
        'pin' => null,
    ]);
});
