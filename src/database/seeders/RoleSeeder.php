<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * ロールの初期データを登録する
     */
    public function run(): void
    {
        /*
         * updateOrCreate():すでに同じcodeのロールがあれば更新なければ新規作成する。
         * ※Seederを複数回実行しても重複しない。
         */
        Role::updateOrCreate(
            // code を検索条件にするため、1つ目の配列に書く
            // 同じcodeがあれば 二個目の [] で更新、なければ新規登録になる
            ['code' => Role::CODE_ADMIN],
            [
                'name' => '管理者',
                'is_system' => true,
                'is_active' => true,
            ]
        );

        Role::updateOrCreate(
            ['code' => Role::CODE_GENERAL],
            [
                'name' => '一般',
                'is_system' => true,
                'is_active' => true,
            ]
        );
    }
}
