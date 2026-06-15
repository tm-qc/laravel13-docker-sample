<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    /**
     * フォームリクエスト実行可否
     *
     * true  : 実行許可
     * false : 403エラー
     *
     * 今回はログイン機能未実装のため true で常に許可
     *
     * 実装例）
     * - ログインユーザーのみ更新可能にしたいときは、ログイン実装後に以下のようにする
     * return auth()->check();
     *
     * - 管理者のみ許可
     * return auth()->user()?->is_admin;
     *
     * - 自分のデータだけ更新可能
     * return auth()->id() === (int) $this->route('id');
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            // TODO:まだテーブルにアイコン、プロフィールはカラム追加してないのであとで
            // 'profile' => ['nullable'],
            // 'icon_image' => [
            //     'nullable',
            //     'image',
            //     'mimes:jpg,jpeg,png,webp',
            //     'max:2048',
            // ],
        ];
    }
}
