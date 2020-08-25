<?php

namespace Tests\Feature\Http\Api;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SignupTest extends TestCase
{
    // このトレイトはテスト実行中にトランザクション貼ってくれ、
    // テスト後にロールバックしてくれるものです。テスト後もキレイなDBを保ってくれます。
    use DatabaseTransactions;

    // private $existEmail;

    // public function setup(): void
    // {
    //     parent::setup();

    //     $this->existEmail = factory(\App\Eloquents\Friend::class)->create()->email;
    // }

    /**
     * @test
     */
    public function signupの正常系を確認する()
    {
        // 送信データを定義
        $postData = [
            'email' => 'test@hoge.com',
            'password' => 'password',
            'nickname' => 'nickname',
        ];

        // API実行
        $response = $this->json('POST', route('api.signup.post'), $postData);

        // レスポンスアサート
        $response->assertStatus(201)
            ->assertJson([
                // サーバサイドで振られるIDは確認しようがないため除外
                'email' => $postData['email'],
                'nickname' => $postData['nickname'],
            ]);

        // DBアサート（firendsテーブルにデータ登録ができていることを確認。passwordは暗号化されているため、除外）
        $this->assertDatabaseHas('friends', [
            'email' => $postData['email'],
            'nickname' => $postData['nickname'],
        ]);
    }

    /**
     * @test
     */
    public function バリデーションテスト（emailがnullの場合は422エラー）()
    {
        $postData = [
            'email' => null,
            'password' => 'password',
            'nickname' => 'nickname',
        ];

        $response = $this->json('POST', route('api.signup.post'), $postData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'email'
            ]);
    }

    /**
     * @test
     */
    public function 異常系_DBへのインサートでエラーになった場合は500エラー()
    {
        // Firendクラスをモックして、store()の挙動を差し替え
        $this->mock(\App\Eloquents\Friend::class, function ($mock) {
            $mock->shouldReceive('store')
                ->once() // １回だけ呼ばれること
                ->withAnyArgs() // 今回はエラーパターンのため、引数は何でもOK（チェックはしない）
                ->andThrow(new \Exception()); // Exceptionをthrowするように変更
        });

        // 送信データを定義
        $postData = [
            'email' => 'test@hoge.com',
            'password' => 'password',
            'nickname' => 'nickname',
        ];

        // API実行
        $response = $this->json('POST', route('api.signup.post'), $postData);

        // アサート
        $response->assertStatus(500);

        // DBアサート（firendsテーブルにデータ登録されていないことを確認）
        $this->assertDatabaseMissing('friends', [
            'email' => $postData['email'],
            'nickname' => $postData['nickname'],
        ]);
    }

    // /**
    //  * @test
    //  * @dataProvider additionProvider
    //  */
    // public function バリデーションテスト($errorField, $postData)
    // {
    //     $response = $this->json('POST', route('api.signup.post'), $postData);

    //     $response->assertStatus(422)
    //         ->assertJsonValidationErrors([
    //             $errorField
    //         ]);
    // }

    // public function additionProvider()
    // {
    //     return [
    //         'emailがnullの場合' => ['email', $this->validParams(['email' => null])],
    //         'email項目自体がない' => ['email', Arr::only($this->validParams(), ['password', 'nickname'])],
    //         'emailが100文字以上の場合' => ['email', $this->validParams(['email' => Str::random(101)])],
    //         'emailがすでに登録されている場合' => ['email', $this->validParams(['email' => $this->existEmail])],
    //         'passwordがnullの場合' => ['password', $this->validParams(['password' => null])],
    //         // 省略
    //     ];
    // }

    // private function validParams($overrides = [])
    // {
    //     return array_merge([
    //         'email' => Str::random(49) . '@' . Str::random(29) . '.' . Str::random(20), // 100文字になるメールアドレス
    //         'password' => Str::random(100),
    //         'nickname' => Str::random(50),
    //     ], $overrides);
    // }
}
