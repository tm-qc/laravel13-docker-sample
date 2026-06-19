<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
         * 管理者ロールを取得する。
         *
         * firstOrFail():
         * RoleSeeder が正しく実行されていない場合は例外にする。
         * ロールがないままユーザーを作ると不整合になるため。
         */
        $adminRole = Role::where('code', Role::CODE_ADMIN)->firstOrFail();

        /*
         * 一般ロールを取得する。
         */
        $generalRole = Role::where('code', Role::CODE_GENERAL)->firstOrFail();

        /*
         * 管理者ユーザーを作成する。
         *
         * updateOrCreate():
         * Seederを複数回実行しても、同じメールアドレスのユーザーが重複しないようにする。
         *
         * password:
         * Userモデル側で password を hashed キャストしているため、
         * ここでは平文を入れても保存時にハッシュ化される。
         */
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'role_id' => $adminRole->id,
                'name' => '管理者ユーザー',
                'password' => 'password',
                'icon_image_path' => '',
                'profile' => '管理者権限を持つサンプルユーザーです。',
            ]
        );

        /*
         * 一般ユーザーを作成する。
         */
        User::updateOrCreate(
            ['email' => 'general@example.com'],
            [
                'role_id' => $generalRole->id,
                'name' => '一般ユーザー',
                'password' => 'password',
                'icon_image_path' => '',
                'profile' => '一般権限を持つサンプルユーザーです。',
            ]
        );
    }
}
