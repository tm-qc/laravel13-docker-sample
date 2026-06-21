<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

//登録許可項目
#[Fillable([
    'role_id',
    'name',
    'email',
    'password',
    'icon_image_path',
    'profile',
])]

//JSON化したときに隠す項目(出さなくていいものは出さないほうが安全)
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /**
     * Userを取得するときに常に読み込むリレーション
     *
     * role はPolicyや画面表示でよく使うため、
     * 毎回 with('role') を書かなくてもよいようにする。
     *
     * 事前に使うことが分かっているリレーションは with() でEager Loading(事前読み込み)することでSQL回数を減らせる
     * ※何もしない通常はLazy Loading(遅延読み込み)
     * @see https://laravel.com/docs/13.x/eloquent-relationships?utm_source=chatgpt.com#eager-loading
     */
    protected $with = [
        'role',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', //hash化
        ];
    }

    /**
     * アイコン画像の表示用URLを取得する(icon_image_path)
     *
     * DBには users/xxxx.jpg のような保存パスだけ持たせる
     * 画面で使うURLはこのアクセサで作る
     *
     * @return Attribute
     */
    protected function iconImageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                // アイコン画像が未登録の場合はデフォルト画像を返す
                // アプリで最初から用意しておく固定画像：public/images配下
                if ($this->icon_image_path === '') {
                    return asset('images/users/default-user-icon.png');
                }

                // 現在のFILESYSTEM_DISKに合わせてURLを作成する
                // ローカル public なら /storage/users/xxxx.jpg
                // 本番 s3 なら S3のURL
                return Storage::url($this->icon_image_path);
            }
        );
    }

    /**
     * リレーション（子）
     * ユーザーに紐づくロールを取得する
     *
     * users.role_id から roles.id に紐づく。
     * 1ユーザーは1つのロールを持つ。
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Policy：管理者ユーザーか判定する
     *
     * Policyや画面表示制御で毎回 role.code を直接比較すると、
     * 判定条件があちこちに散らばるため、Userモデルにまとめる。
     * @see https://laravel.com/docs/13.x/authorization#generating-policies
     *
     * @return boolean
     */
    public function isAdmin(): bool
    {
        //リレーションで取得した rolesのcodeで判定
        return $this->role?->code === Role::CODE_ADMIN;
    }

    /**
     * 一般ユーザーか判定する
     *
     * @return boolean
    */
    public function isGeneral(): bool
    {
        return $this->role?->code === Role::CODE_GENERAL;
    }
}
