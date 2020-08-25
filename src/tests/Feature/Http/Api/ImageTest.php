<?php

namespace Tests\Feature\Http\Api;

use App\Eloquents\Friend;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImageTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function 正常系を確認する()
    {
        $friend = factory(Friend::class)->create();

        \Storage::fake('local');

        $response = $this->actingAs($friend, 'api')
            ->json('POST', route('api.my.image.post'), [
                'file' => UploadedFile::fake()->image('test.png')->size(250),
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'image_url' => route('web.image.get', ['friendId' => $friend->id])
            ]);

        $uploaded = Friend::find($friend->id)->image_path;
        \Storage::disk('local')->assertExists($uploaded);
    }

    /**
     * @test
     */
    public function 異常系_DBへのアクセスでエラーになった場合は500エラー()
    {
        \Storage::fake('local');

        // Firendクラスをモック
        $this->mock(\App\Eloquents\Friend::class, function ($mock) {
            $mock->shouldReceive('imageStore')
                ->once()
                ->withAnyArgs()
                ->andThrow(new \Exception());
        });

        $friend = factory(Friend::class)->create();

        $response = $this->actingAs($friend, 'api')
            ->json('POST', route('api.my.image.post'), [
                'file' => UploadedFile::fake()->image('test.png')->size(250),
            ]);

        $response->assertStatus(500);

        // 登録されていないこと
        $path = Friend::find($friend->id)->image_path;
        $this->assertEquals(null, $path);

        // ファイルが登録されていないこと
        $fileCount = count(\Storage::disk('local')->allFiles('images'));
        $this->assertEquals(0, $fileCount);
    }
}
