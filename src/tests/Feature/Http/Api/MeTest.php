<?php

namespace Tests\Feature\Http\Api;

use App\Eloquents\Friend;
use App\Eloquents\Pin;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MeTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function 正常系を確認する()
    {
        $friend = factory(Friend::class)->create([
            'nickname' => 'some data',
            'email' => 'test@hoge.com',
            'image_path' => '/test/hoge/fuga.jpg',
        ]);
        $pin = factory(Pin::class)->create([
            'friends_id' => $friend->id,
        ]);

        $response = $this->actingAs($friend, 'api')
            ->json('GET', route('api.me.get'));

        $response->assertStatus(200)
            ->assertJson([
                'id' => $friend->id,
                'nickname' => $friend->nickname,
                'email' => $friend->email,
                'image_url' => route('web.image.get', [
                    'friendId' => $friend->id,
                    't' => $friend->updated_at->getTimestamp()
                ]),
                'pin' => [
                    'datetime' => $pin->created_at->toIso8601String(),
                    'latitude' => $pin->latitude,
                    'longitude' => $pin->longitude,
                ],
            ]);
    }

    /**
     * @test
     */
    public function 正常系を確認する_データがnullのフィールドがある場合()
    {
        $friend = factory(Friend::class)->create([
            'image_path' => null,
        ]);

        $response = $this->actingAs($friend, 'api')
            ->json('GET', route('api.me.get'));

        $response->assertStatus(200)
            ->assertJson([
                'id' => $friend->id,
                'nickname' => $friend->nickname,
                'email' => $friend->email,
                'image_url' => null, // ココを確認
                'pin' => null, // ココを確認
            ]);
    }

    /**
     * @test
     */
    public function 異常系_なんらかの理由によりデータが消えていた場合()
    {
        // meに関しては、ありえないとは思うが、Token取得後（ログイン後）にデータが消えている場合。

        $friend = factory(Friend::class)->create();

        Friend::destroy($friend->id);

        $response = $this->actingAs($friend, 'api')
            ->json('GET', route('api.me.get'));

        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function 異常系_DBへのアクセスでエラーになった場合は500エラー()
    {
        // Firendクラスをモック
        $this->mock(\App\Eloquents\Friend::class, function ($mock) {
            $mock->shouldReceive('findById')
                ->once()
                ->withAnyArgs()
                ->andThrow(new \Exception());
        });

        $friend = factory(Friend::class)->create();

        $response = $this->actingAs($friend, 'api')
            ->json('GET', route('api.me.get'));

        $response->assertStatus(500);
    }
}
