<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    /**
     * フォームリクエスト実行可否
     *
     * true  : 実行許可
     * false : 403エラー
     *
     * 今回はログイン機能未実装のため true で常に許可
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 更新時のバリデーションルール
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',

                /*
                 * 更新時のメールアドレス重複チェック
                 *
                 * users.email の重複を確認する。
                 * ただし、現在編集中のユーザ自身は除外する。
                 *
                 * resourceルートでは {user} なので、
                 * $this->route('user') で現在のUserモデルを取得できる。
                 */
                Rule::unique('users', 'email')->ignore($this->route('user')),
            ],

            /*
             * 更新時のパスワードは任意
             *
             * 未入力なら変更しない。
             * 入力された場合だけ、確認用パスワード一致と8文字以上をチェックする。
             */
            'password' => ['nullable', 'string', 'confirmed', 'min:8'],
            'profile' => ['string', 'max:500'],
            'icon_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }

    /**
     * バリデーション前に入力値を整える
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            /*
             * null→空文字変換
             *
             * profileは任意入力だが、アプリ内では null ではなく空文字で統一する。
             * ここで必ず profile キーを作ることで、
             * ControllerやServiceで空文字変換を不要にできる。
             */
            'profile' => $this->input('profile') ?? '',
        ]);
    }
}
