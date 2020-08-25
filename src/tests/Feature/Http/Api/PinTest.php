<?php

namespace Tests\Feature\Http\Api;

use App\Eloquents\Friend;
use App\Eloquents\FriendsRelationship;
use App\Eloquents\Pin;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\TestCase;

class PinTest extends TestCase
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
    public function 正常系()
    {
        $now = Carbon::now();
        Carbon::setTestNow($now); // 現在時刻を固定

        $postJson = [
            'latitude' => 22.000001,
            'longitude' => 144.000001,
        ];
        $beforePosition = [
            'latitude' => 33.333333,
            'longitude' => 133.444444,
        ];

        // pinを予め持っていると想定
        factory(Pin::class)->create([
            'friends_id' => $this->me->id,
            'latitude' => $beforePosition['latitude'],
            'longitude' => $beforePosition['longitude'],
        ]);
        // すでに友だちもいる状態
        factory(FriendsRelationship::class, 3)->create([
            'own_friends_id' => $this->me->id,
        ]);
        // 5分以内にPinを打った、近くの人２名（この２名が友だちになる想定）
        $friends = Collection::times(2, function ($number) use ($now, $postJson) {
            return factory(Friend::class, 1)
                ->create([
                    'image_path' => $number === 1 ? 'dummy/test.png' : null,
                ])
                ->each(function ($friend) use ($now, $postJson) {
                    factory(Pin::class)->create([
                        'friends_id' => $friend->id,
                        // 同じ座標とする
                        'latitude' => $postJson['latitude'],
                        'longitude' => $postJson['longitude'],
                        // ちょうど5分前に登録したとする
                        'created_at' => $now->copy()->subMinutes(5),
                    ]);
                })
                ->first();
        });
        // 5分１秒前にPinを打った、近くの人１名
        factory(Friend::class, 1)
            ->create()
            ->each(function ($friend) use ($now, $postJson) {
                factory(Pin::class)->create([
                    'friends_id' => $friend->id,
                    // 同じ座標とする
                    'latitude' => $postJson['latitude'],
                    'longitude' => $postJson['longitude'],
                    // 5分１秒前に登録したとする
                    'created_at' => $now->copy()->subMinutes(5)->subSeconds(1),
                ]);
            });
        // 5分以内にPinを打った、遠くの人１名
        factory(Friend::class, 1)
            ->create()
            ->each(function ($friend) use ($now, $postJson) {
                factory(Pin::class)->create([
                    'friends_id' => $friend->id,
                    // 離れた座標とする
                    'latitude' => $postJson['latitude'] + 1,
                    'longitude' => $postJson['longitude']+ 1,
                    // ちょうど5分前に登録したとする
                    'created_at' => $now->copy()->subMinutes(5),
                ]);
            });

        $response = $this->actingAs($this->me, 'api')
            ->json('POST', route('api.my.pin.post'), $postJson);

        $response->assertStatus(200)
            ->assertJson(
                $friends->map(function ($friend) {
                    $friend->load('pin');

                    $url = $friend->image_path
                        ? route('web.image.get', [
                            'friendId' => $friend->id,
                            't' => $friend->updated_at->getTimestamp()
                        ])
                        : null;

                    return [
                        'id' => $friend->id,
                        'nickname' => $friend->nickname,
                        'email' => $friend->email,
                        'image_url' => $url,
                        'pin' => [
                            'datetime' => $friend->pin->created_at->toIso8601String(),
                            'latitude' => $friend->pin->latitude,
                            'longitude' => $friend->pin->longitude,
                        ],
                    ];
                })
                ->toArray()
            );

        // 友だちになっていること
        $friends->each(function ($firend) {
            $this->assertDatabaseHas('friends_relationships', [
                'own_friends_id' => $this->me->id,
                'other_friends_id' => $firend->id,
            ]);
            $this->assertDatabaseHas('friends_relationships', [
                'own_friends_id' => $firend->id,
                'other_friends_id' => $this->me->id,
            ]);
        });

        // 既存のPinから上書きされていること
        $this->assertDatabaseHas('pins', [
            'friends_id' => $this->me->id,
            'latitude' => $postJson['latitude'],
            'longitude' => $postJson['longitude'],
        ]);
        $this->assertDatabaseMissing('pins', [
            'friends_id' => $this->me->id,
            'latitude' => $beforePosition['latitude'],
            'longitude' => $beforePosition['longitude'],
        ]);
    }

    /**
     * @test
     */
    public function 正常系_初めてPinを打つ_友だちになれる人がいなかった場合は空配列が返る()
    {
        $response = $this->actingAs($this->me, 'api')
            ->json('POST', route('api.my.pin.post'), [
                'latitude' => 33.333333,
                'longitude' => 133.444444,
            ]);

        $response->assertStatus(200)
            ->assertJson([]);

        $this->assertDatabaseHas('pins', [
            'friends_id' => $this->me->id,
            'latitude' => 33.333333,
            'longitude' => 133.444444,
        ]);
    }

    /**
     * @test
     */
    public function 異常系_DBへのアクセスでエラーになった場合は500エラー()
    {
        $beforePin = factory(Pin::class)->create([
            'friends_id' => $this->me->id,
            'latitude' => 22.000001,
            'longitude' => 144.000001,
        ]);

        // Firendクラスをモック
        $this->partialMock(Friend::class, function ($mock) {
            $mock->shouldReceive('findByIds')
                ->withAnyArgs()
                ->andThrow(new \Exception());
        });

        // ファサードはモック機能を持っているので、利用
        // 友だちになる部分だけを動かす
        \Facades\App\Contracts\Distance::shouldReceive('canBeFriends')
            ->withAnyArgs()
            ->andReturn([
                $firendId = factory(Friend::class)->make()->id,
            ]);

        $response = $this->actingAs($this->me, 'api')
            ->json('POST', route('api.my.pin.post'), [
                'latitude' => 33.333333,
                'longitude' => 133.444444,
            ]);

        $response->assertStatus(500);

        // 登録されていないこと
        $this->assertDatabaseMissing('friends_relationships', [
            'own_friends_id' => $this->me->id,
            'other_friends_id' => $firendId,
        ]);
        $this->assertDatabaseMissing('friends_relationships', [
            'own_friends_id' => $firendId,
            'other_friends_id' => $this->me->id,
        ]);
        $this->assertDatabaseMissing('pins', [
            'friends_id' => $this->me->id,
            'latitude' => 33.333333,
            'longitude' => 133.444444,
        ]);
        $this->assertDatabaseHas('pins', $beforePin->only([
            'friends_id',
            'latitude',
            'longitude',
        ]));
    }
}
