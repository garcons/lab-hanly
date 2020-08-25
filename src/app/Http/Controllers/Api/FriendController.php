<?php

namespace App\Http\Controllers\Api;

use App\Eloquents\Friend;
use App\Eloquents\FriendsRelationship;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FriendShowRequest;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    protected $friend;
    protected $relationship;

    public function __construct(Friend $friend, FriendsRelationship $relationship)
    {
        $this->friend = $friend;
        $this->relationship = $relationship;
    }

    /**
     * @param \App\Http\Requests\Api\FriendShowRequest $request
     * @param int $friendId
     * @return \App\Http\Resources\FriendResource
     */
    public function show(FriendShowRequest $request, int $friendId)
    {
        $friend = $this->friend->findById($friendId);

        return new \App\Http\Resources\FriendResource($friend);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\FriendCollection
     */
    public function list(Request $request)
    {
        $myId = $request->user()->id;

        $friendIds = $this->relationship->myFriends($myId)->pluck('other_friends_id')->toArray();
        $friends = $this->friend->findByIds($friendIds);

        return new \App\Http\Resources\FriendCollection($friends);
    }
}
