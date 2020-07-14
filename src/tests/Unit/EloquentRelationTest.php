<?php

namespace Tests\Unit;

use Tests\TestCase;

class EloquentRelationTest extends TestCase
{
    /**
     * @test
     */
    public function リレーションすげーよ()
    {
        // Friendが１のやつを取得
        $friend = \App\Eloquents\Friend::find(1);

        // EloquentのFriend.phpで設定したメソッド名でアクセス
        // たったこれだけで、FriendのID１に紐づく、FriendsRelationshipのデータが取得できる！！
        $relationships = $friend->relationship;

        // １対多の「多」を取得しているので、Collectionオブジェクトだからループできる
        $myFriendIds = [];
        foreach ($relationships as $myFriend) {
            $myFriendIds[] = $myFriend->other_friends_id; // 友だちIDだけを取得
        }

        // ddで見てみよう
        // dd($myFriendIds);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function コレクション使えばこんな短くなる！↑のやつと同じことしてます。()
    {
        $myFriendIds = \App\Eloquents\Friend::find(1)
            ->relationship
            ->pluck('other_friends_id');

        // dd($myFriendIds);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function Friend経由でPinの座標を取得()
    {
        $pin = \App\Eloquents\Friend::find(1)->pin;

        // dd($pin->toArray()); // toArray()でarrayにしてくれます。

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function Pin経由でFriendのニックネームを取得()
    {
        // get()でとるとCollectionになるので、first()で取得。first()は最初の１件を取得する。
        $friend = \App\Eloquents\Pin::where('friends_id', 1)->first()->friend;

        // dd($friend->nickname);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function Pin経由でFriendRelationShipの友だち（other_id）を取得()
    {
        // 一旦firendを取得
        $friend = \App\Eloquents\Pin::where('friends_id', 1)->first()->friend;
        // firendに紐づくrelationshipの友だちIDを取得
        $otherFriendIds = $friend->relationship->pluck('other_friends_id');

        // dd($otherFriendIds);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function さらにそこから友だちの名前を取得()
    {
        // // １つ目のやりかた
        // $friend = \App\Eloquents\Pin::where('friends_id', 1)->first()->friend;
        // $otherFriendIds = $friend->relationship->pluck('other_friends_id');

        // // ここまでは、↑のメソッドと同じ

        // $otherFriends = \App\Eloquents\Friend::whereIn('id', $otherFriendIds)->get();

        // dd($otherFriends->pluck('nickname'));

        // // ２つ目のやり方（Collectionの機能を使った場合）
        // $otherFriendNickname = \App\Eloquents\Pin::where('friends_id', 1)
        //     ->first()
        //     ->friend
        //     ->relationship
        //     ->map(function ($relation) {
        //         // @see \App\Eloquents\FriendsRelationship::otherFriend
        //         return $relation->otherFriend; // otherFriendという別のリレーション定義を付け加えているので、こんな書き方もできる
        //     })
        //     ->pluck('nickname');

        // dd($otherFriendNickname);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function Pinを持っているFriendを取得()
    {
        $friends = \App\Eloquents\Friend::whereHas('pin')->get();

        // dd($friends->toArray());

        $this->assertTrue(true);
    }
}
