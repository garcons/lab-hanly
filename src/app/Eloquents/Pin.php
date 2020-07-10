<?php

namespace App\Eloquents;

use Illuminate\Database\Eloquent\Model;

class Pin extends Model
{
    protected $table = 'pins';

    protected $fillable = [
        'friends_id',
        'latitude',
        'longitude',
    ];
}
