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
}
