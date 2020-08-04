<?php

namespace App\Eloquents;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Friend extends Authenticatable
{
    use HasApiTokens;

    // 実際のテーブルが、クラス名の複数形＋スネークケースであれば、書かなくてOK
    protected $table = 'friends';

    // Eloquentを通して更新や登録が可能なフィールド（ホワイトリストを定義）
    protected $fillable = [
        'nickname', 'email', 'password', 'image_path'
    ];

    // JSONとしてレスポンスしてはダメな項目を定義
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function relationship()
    {
        return $this->hasMany(\App\Eloquents\FriendsRelationship::class, 'own_friends_id', 'id');
    }

    public function pin()
    {
        return $this->hasOne(\App\Eloquents\Pin::class, 'friends_id', 'id');
    }
}
