<?php

namespace App\Eloquents;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class FriendsRelationship extends Model
{
    protected $table = 'friends_relationships';

    protected $fillable = [
        'own_friends_id',
        'other_friends_id',
    ];

    /**
     * @param int $friendId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function myFriends(int $friendId): Collection
    {
        return $this->newInstance()
            ->where('own_friends_id', $friendId)
            ->get();
    }

    /**
     * @param int $ownId
     * @param int $otherId
     * @return void
     */
    public function getAlongWith(int $ownId, int $otherId): void
    {
        $myRelation = $this->newInstance();
        $myRelation->fill([
           'own_friends_id' => $ownId,
           'other_friends_id' => $otherId,
        ]);
        $myRelation->save();
    }

    public function friend()
    {
        return $this->belongsTo(\App\Eloquents\Friend::class, 'own_friends_id', 'id');
    }

    public function otherFriend()
    {
        return $this->belongsTo(\App\Eloquents\Friend::class, 'other_friends_id', 'id');
    }
}
