<?php

namespace App\Policies;

use App\Eloquents\Friend;
use Illuminate\Auth\Access\HandlesAuthorization;

class FriendPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function view(Friend $user, Friend $friend)
    {
        // 友だちの一覧に「私」が存在するかをチェック
        return $friend->relationship->where('other_friends_id', $user->id)->count() > 0;
    }
}
