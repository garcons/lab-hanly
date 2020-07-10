<?php

namespace App\Eloquents;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    // 実際のテーブルが、クラス名の複数形＋スネークケースであれば、書かなくてOK
    protected $table = 'friends';

    // Eloquentを通して更新や登録が可能なフィールド（ホワイトリストを定義）
    protected $fillable = [
        'nickname', 'email', 'password', 'image_path'
    ];
}
