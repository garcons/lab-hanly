<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class FriendShowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // パスパラメータを取得
        $friendId = $this->route('friendId');
        // Eloquentを使って、閲覧しようとしている友だち情報を取得
        $friend = \App\Eloquents\Friend::find($friendId);

        // firendが取れた場合に、認可チェックを行う
        return $friend && $this->user()->can('view', $friend);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
