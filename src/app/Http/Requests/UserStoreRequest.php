<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
{
    /**
     * フォームリクエスト実行可否
     *
     * true  : 実行許可
     * false : 403エラー
     *
     * 実装例）
     * - ログインユーザーのみ更新可能にしたいときは、ログイン実装後に以下のようにする
     * return auth()->check();
     *
     * - 管理者のみ許可(ポリシーじゃない場合)
     * return auth()->user()?->is_admin;
     *
     * - 自分のデータだけ更新可能
     * return auth()->id() === (int) $this->route('id');
     */
    public function authorize(): bool
    {
        // ユーザー新規登録は管理者のみOK
        // ※User::classは捜査中のユーザ。編集対象ユーザーがいないときに使う。
        return $this->user()?->can('create', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'role_id' => [
                'required',
                'integer',

                /**
                 * rolesテーブルでアクティヴ(使用可能) & 存在するidだけ許可する。
                 * 不正なrole_idを直接POSTされても登録できないようにする。
                 */
                Rule::exists('roles', 'id')->where('is_active', true),
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
            'profile' => ['string', 'max:500'],
            'icon_image' => [
                'nullable', // 画像未選択の場合はnull扱いになるためnullableを付与
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
            /**
             * null→空文字変換
             *
             * profileは任意入力だが、アプリ内では null ではなく空文字で統一する。
             * ここで必ず profile キーを作ることで、 ControllerやServiceで登録する場合の空文字変換を不要にできる
             *
             * 例）
             * 'profile' => $validated['profile'] ?? '',
             * ↓
             * 'profile' => $validated['profile'],
            */
            'profile' => $this->input('profile') ?? '',
        ]);
    }

    /**
     * バリデーションエラーメッセージを定義する
     *
     * 画像のサイズが大きい場合に src/lang/ja/validation.php で
     *
     * [ 'uploaded' => ':attribute のアップロードに失敗しました。', ]
     *
     * が先に表示されて
     *
     * max の [ 'file' => ':attribute のサイズは :max キロバイト以下にしてください。', ] が表示されない
     *
     * 一旦 src/lang/ja/validation.php で icon_image の uploaded のカスタムメッセを入れて対応する。
     * もし max できちんと出したいときは以下を共通化も視野に入れて検討する。
     */
    // public function messages(): array
    // {
    //     return [
    //         'icon_image.max' => 'アイコン画像は2MB以下の画像を指定してください。',
    //     ];
    // }
}
