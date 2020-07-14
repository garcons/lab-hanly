<?php

namespace Tests\Unit;

use Tests\TestCase;

class EloquentTest extends TestCase
{
    /**
     * @test
     */
    public function IDを指定して１件取得()
    {
        $friend = \App\Eloquents\Friend::find(1);

        // dd($friend);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function 全件取得()
    {
        $friends = \App\Eloquents\Friend::all();

        // dd($friends);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function 条件を指定して取得（ニックネームにmatsuを含む人）()
    {
        // これはCollectionになるのです！
        $friends = \App\Eloquents\Friend::where('nickname', 'like', '%matsu%')->get();

        // dd($friends);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function Friendに１件データを登録()
    {
        // ↓のコメントアウトのどちらかを外して試してみてね

        // // １つ目のやり方
        // $newFriend = new \App\Eloquents\Friend();
        // $newFriend->fill([
        //     'nickname' => 'たなーーーか',
        //     'email' => 'hogefuga2@piyo.com',
        //     'password' => bcrypt('passsword-desu'),
        //     'image_path' => null,
        //     'remenmber_token' => \Str::random(80), // これはfillable無いので、無視される
        // ])->save();

        // // ２つ目のやり方
        // $newFriend = \App\Eloquents\Friend::create([
        //     'nickname' => 'たなーーーか',
        //     'email' => 'hogefuga3@piyo.com',
        //     'password' => bcrypt('passsword-desu'),
        //     'image_path' => null,
        //     'remenmber_token' => \Str::random(80), // これはfillable無いので、無視される
        // ]);

        // dd($newFriend);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function Friendのデータを更新()
    {
        // ↓のコメントアウトのどちらかを外して試してみてね

        // // １つ目のやり方
        // $updated = \App\Eloquents\Friend::find(1)
        //     ->fill([
        //         'nickname' => 'matsumatsu',
        //     ])
        //     ->save();

        // // true or false が返る
        // dd($updated);

        // // ２つ目のやり方
        // $friend = \App\Eloquents\Friend::find(1); // 一旦findだけして変数詰めて、オブジェクトを操作すれば、更新後のデータを見れる
        // $friend->fill([
        //         'nickname' => 'matsumatsu2',
        //     ])
        //     ->save();

        // dd($friend);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function FriendのID２のデータを削除()
    {
        // // IDを指定して削除する場合
        // $delete = \App\Eloquents\Friend::destroy(2);

        // // 削除した件数が返る
        // dd($delete);

        // // いろんな条件で検索して削除する場合
        // $delete = \App\Eloquents\Friend::where('nickname', 'like', '%matsu%')->delete();
        // dd($delete);

        $this->assertTrue(true);
    }
}
