<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

//登録許可項目
#[Fillable([
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
     * DBには users/xxxx.jpg のような保存パスだけ持たせる。
     * 画面で使うURLはこのアクセサで作る。
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
}
