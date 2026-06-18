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
            ['code' => Role::CODE_ADMIN],
            ['name' => '管理者']
        );

        Role::updateOrCreate(
            ['code' => Role::CODE_GENERAL],
            ['name' => '一般']
        );
    }
}
