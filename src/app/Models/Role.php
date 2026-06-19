<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'code',
    'name',
    'is_system',
    'is_active',
])]

class Role extends Model
{
    /**
     * 管理者ロールのコード
     *
     * roles.code に保存する値。
     * UserモデルやPolicyで文字列を直接書かないために定数化している。
    */
    public const CODE_ADMIN = 'admin';

    /**
     * 一般ロールのコード
     *
     * roles.code に保存する値。
     * UserモデルやPolicyで文字列を直接書かないために定数化している。
    */
    public const CODE_GENERAL = 'general';

    /**
     * キャスト定義
     *
     * MySQLのDBでは boolean が 0 / 1 で保存されるため、
     * PHP側ではモデルで true / false として扱えるように変換する。
     *
     * @return array<string, string>
    */
    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * リレーション（親）
     * このロールに紐づくユーザー一覧を取得する
     *
     * roles.id に対して users.role_id が紐づく。
     * Role は複数の User を持つ。
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
