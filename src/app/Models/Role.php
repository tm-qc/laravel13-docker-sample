<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'code',
    'name',
])]

class Role extends Model
{
    /**
     * 管理者ロールのコード
     */
    public const CODE_ADMIN = 'admin';

    /**
     * 一般ロールのコード
     */
    public const CODE_GENERAL = 'general';

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
