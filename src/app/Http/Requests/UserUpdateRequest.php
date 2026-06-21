<?php

namespace App\Http\Requests;

use App\Models\User;
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
        // ルーティングから編集対象ユーザを取得
        // 管理者かどうか、自分自身かどうかの判定は Policy 側で行う。
        // 管理者じゃない場合、ポリシーの判定で $targetUser を使うため、ルーティングから取得が必要
        // ※ 一般ユーザーの場合に「自分自身かどうか」を判定する
        $targetUser = $this->route('user');
        return $this->user()?->can('update', $targetUser);

    }

    /**
     * 更新時のバリデーションルール
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        // ロール変更できるか判定する
        $canChangeRole = $this->user()?->can('changeRole', User::class) ;

        return [
            // 管理者だけ role_id を送信可能にする判定
            'role_id' => [
                /**
                 * ポリシーの判定を使って、管理者以外の場合、role_id をバリデーション済みデータから除外する。
                 *
                 * 一般ユーザーが直接POSTで role_id を送っても、
                 * Service側の更新対象データに role_id が入らないようにする。
                 *
                 * 管理者の場合だけ、正しいロールIDで更新できるようにする。
                */
                Rule::excludeIf(!$canChangeRole),
                'required',
                'integer',

                /**
                 * rolesテーブルに存在し、かつ有効なロールだけ許可する。
                 *
                 * 画面で非表示にしていてもPOST値は書き換え可能なので、
                 * サーバー側でもチェックする。
                 */
                Rule::exists('roles', 'id')->where('is_active', true),
            ],
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
