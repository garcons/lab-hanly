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

    public function friend()
    {
        return $this->belongsTo(\App\Eloquents\Friend::class, 'own_friends_id', 'id');
    }

    public function otherFriend()
    {
        return $this->belongsTo(\App\Eloquents\Friend::class, 'other_friends_id', 'id');
    }
}
