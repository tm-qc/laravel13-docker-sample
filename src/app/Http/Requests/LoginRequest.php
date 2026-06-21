<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * フォームリクエスト実行可否
     * ※ログイン画面のなので true のままでOK
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * ログインフォームのバリデーションルールを定義
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        // ここで定義されてるものが Auth::attempt($credentials) に渡され「認証条件」として使われる
        // ※password はLaravel側が入力パスワードとDBのハッシュ値を比較してくれます
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }
}
