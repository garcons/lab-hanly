<?php

namespace Tests\Feature\Http\Web;

use App\Eloquents\Friend;
use App\Eloquents\FriendsRelationship;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImageTest extends TestCase
{
    use DatabaseTransactions;

    private $me;

    public function setup(): void
    {
        parent::setup();

        $this->me = factory(Friend::class)->create();
    }

    private function getAlongWith($friendId)
    {
        // 友だち関係を作る
        factory(FriendsRelationship::class)->create([
            'own_friends_id' => $this->me->id,
            'other_friends_id' => $friendId,
        ]);
        factory(FriendsRelationship::class)->create([
            'own_friends_id' => $friendId,
            'other_friends_id' => $this->me->id,
        ]);
    }

    /**
     * @test
     */
    public function 正常系()
    {
        \Storage::fake('local');
        $path = \Storage::disk('local')->put('images', UploadedFile::fake()->image('test.png')->size(250));

        $friend = factory(Friend::class)->create([
            'image_path' => $path
        ]);
        $this->getAlongWith($friend->id);

        $response = $this->actingAs($this->me, 'web')
            ->get(route('web.image.get', [
                'friendId' => $friend->id,
                't' => $friend->updated_at->getTimestamp()
            ]));

        $response->assertStatus(200)
            ->assertHeader('content-type', \Storage::disk('local')->mimeType($path));

        $this->assertEquals(
            \Str::of($path)->basename(),
            $response->getFile()->getFilename() /** @see SplFileInfo */
        );
    }

    /**
     * @test
     */
    public function 異常系_ファイル登録していないユーザにアクセスした場合は404エラー()
    {
        $friend = factory(Friend::class)->create([
            'image_path' => null, //　ファイルを登録していない
        ]);
        $this->getAlongWith($friend->id);

        $response = $this->actingAs($this->me, 'web')
            ->get(route('web.image.get', [
                'friendId' => $friend->id,
                't' => $friend->updated_at->getTimestamp()
            ]));

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function 異常系_ファイルへのアクセスでエラーになった場合は500エラー()
    {
        $friend = factory(Friend::class)->create([
            'image_path' => 'dummy/hoge.png', //　存在しないファイル（or 実ファイルが消えているパス）
        ]);
        $this->getAlongWith($friend->id);

        $response = $this->actingAs($this->me, 'web')
            ->get(route('web.image.get', [
                'friendId' => $friend->id,
                't' => $friend->updated_at->getTimestamp()
            ]));

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
        $this->getAlongWith($friend->id);

        $response = $this->actingAs($this->me, 'web')
            ->get(route('web.image.get', [
                'friendId' => $friend->id,
                't' => $friend->updated_at->getTimestamp()
            ]));

        $response->assertStatus(500);
    }
}
