<?php

namespace Tests\Feature\Http\Api;

use App\Eloquents\Friend;
use App\Eloquents\FriendsRelationship;
use App\Eloquents\Pin;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FriendShowTest extends TestCase
{
    use DatabaseTransactions;

    private $me;

    public function setup(): void
    {
        parent::setup();

        // 頻出する、かつ各テストケースで変わらないものは、ここに書いてまとめるのもありです
        $this->me = factory(Friend::class)->create();
    }

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
        $friendsPin = factory(Pin::class)->create([
            'friends_id' => $friend->id,
        ]);
        // 友だち関係を作る
        factory(FriendsRelationship::class)->create([
            'own_friends_id' => $this->me->id,
            'other_friends_id' => $friend->id,
        ]);
        factory(FriendsRelationship::class)->create([
            'own_friends_id' => $friend->id,
            'other_friends_id' => $this->me->id,
        ]);

        $response = $this->actingAs($this->me, 'api')
            ->json('GET', route('api.friends.get', ['friendId' => $friend->id]));

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
                    'datetime' => $friendsPin->created_at->toIso8601String(),
                    'latitude' => $friendsPin->latitude,
                    'longitude' => $friendsPin->longitude,
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
        factory(FriendsRelationship::class)->create([
            'own_friends_id' => $this->me->id,
            'other_friends_id' => $friend->id,
        ]);
        factory(FriendsRelationship::class)->create([
            'own_friends_id' => $friend->id,
            'other_friends_id' => $this->me->id,
        ]);

        $response = $this->actingAs($this->me, 'api')
            ->json('GET', route('api.friends.get', ['friendId' => $friend->id]));

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
    public function 異常系_認可チェック_友だち関係ではない人にアクセスすると403エラー()
    {
        $friend = factory(Friend::class)->create();

        $response = $this->actingAs($this->me, 'api')
            ->json('GET', route('api.friends.get', ['friendId' => $friend->id]));

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function 異常系_なんらかの理由によりデータが消えていた場合は403エラー()
    {
        $friend = factory(Friend::class)->create();

        Friend::destroy($friend->id);

        $response = $this->actingAs($this->me, 'api')
            ->json('GET', route('api.friends.get', ['friendId' => $friend->id]));

        // 本当は404(NotFound)とかでもいいけど、面倒だからこのままにしてます（真似しないでね）
        $response->assertStatus(403);
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
        factory(FriendsRelationship::class)->create([
            'own_friends_id' => $this->me->id,
            'other_friends_id' => $friend->id,
        ]);
        factory(FriendsRelationship::class)->create([
            'own_friends_id' => $friend->id,
            'other_friends_id' => $this->me->id,
        ]);

        $response = $this->actingAs($this->me, 'api')
            ->json('GET', route('api.friends.get', ['friendId' => $friend->id]));

        $response->assertStatus(500);
    }
}
