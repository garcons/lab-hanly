<?php

namespace Tests\Feature\Http\Api;

use App\Eloquents\Friend;
use App\Eloquents\FriendsRelationship;
use App\Eloquents\Pin;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\TestCase;

class FriendListTest extends TestCase
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
        $friends = Collection::times(3, function ($number) {
            return factory(Friend::class, 1) // 件数指定すると、Collectionクラスとなるため、そのままループ可能
                ->create([
                    'nickname' => "some data {$number}",
                    'email' => "test_{$number}@hoge.com",
                    'image_path' => "/test/hoge/fuga_{$number}.jpg",
                ])
                ->each(function ($friend) {
                    factory(Pin::class)->create([
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
                })
                // assertのため想定データを作成
                ->map(function ($friend) {
                    $friend->load('pin');

                    return [
                        'id' => $friend->id,
                        'nickname' => $friend->nickname,
                        'email' => $friend->email,
                        'image_url' => route('web.image.get', [
                            'friendId' => $friend->id,
                            't' => $friend->updated_at->getTimestamp()
                        ]),
                        'pin' => [
                            'datetime' => $friend->pin->created_at->toIso8601String(),
                            'latitude' => $friend->pin->latitude,
                            'longitude' => $friend->pin->longitude,
                        ],
                    ];
                })
                ->first();
        });

        $response = $this->actingAs($this->me, 'api')
            ->json('GET', route('api.friends.list.get'));

        $response->assertStatus(200)
            ->assertJson($friends->toArray());
    }

    /**
     * @test
     */
    public function 正常系を確認する_データがnullのフィールドがある場合()
    {
        $friends = Collection::times(3, function ($number) {
            return factory(Friend::class, 1)
                ->create([
                    'image_path' => null,
                ])
                ->each(function ($friend) {
                    factory(FriendsRelationship::class)->create([
                        'own_friends_id' => $this->me->id,
                        'other_friends_id' => $friend->id,
                    ]);
                    factory(FriendsRelationship::class)->create([
                        'own_friends_id' => $friend->id,
                        'other_friends_id' => $this->me->id,
                    ]);
                })
                // assertのため想定データを作成
                ->map(function ($friend) {
                    $friend->load('pin');

                    return [
                        'id' => $friend->id,
                        'nickname' => $friend->nickname,
                        'email' => $friend->email,
                        'image_url' => null, // ココを確認
                        'pin' => null, // ココを確認
                    ];
                })
                ->first();
        });

        $response = $this->actingAs($this->me, 'api')
            ->json('GET', route('api.friends.list.get'));

        $response->assertStatus(200)
            ->assertJson($friends->toArray());
    }

    /**
     * @test
     */
    public function 正常系を確認する_友だちがいない場合は空配列が返却される()
    {
        $response = $this->actingAs($this->me, 'api')
            ->json('GET', route('api.friends.list.get'));

        $response->assertStatus(200)
            ->assertJson([]);
    }

    /**
     * @test
     */
    public function 異常系_DBへのアクセスでエラーになった場合は500エラー()
    {
        // Firendクラスをモック
        $this->mock(\App\Eloquents\Friend::class, function ($mock) {
            $mock->shouldReceive('findByIds')
                ->once()
                ->withAnyArgs()
                ->andThrow(new \Exception());
        });

        $response = $this->actingAs($this->me, 'api')
            ->json('GET', route('api.friends.list.get'));

        $response->assertStatus(500);
    }
}
