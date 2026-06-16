<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /**
         * テーブル設計の考え方メモ
         *
         * - DBは「null 3値論理（True, False, Unknown）」があるので、基本 not null で設定した方が無難
         * - 必須：not null のみ
         * - 任意：not null + 初期値
         * 　※string：空文字
         * 　※int：0
         * - 値がないときの値は「null or 空文字」で悩まなくてよくなり安全
         *
         * ただし
         * - not nullベースにするかはPJの方針に寄るのであくまでベースの考え方
         * - Laravelで null か 空文字かわからないときは両方判定前提で、開発者が考えるのも普通にある
         * - サービスなどでプログラム側でnullエラーに引っ掛かり、結局空文字変換することもある
         * - この設定はあくまでDBの保護でプログラム側と別に考える。という思考で考えておく
         *
         * 今回はとりあえず not null✛初期値 ベースで行く
        */
        Schema::table('users', function (Blueprint $table) {
            // アイコン画像保存パス(任意：not null + '')
            $table->string('icon_image_path')
                ->default('')
                ->after('password')
                ->comment('アイコン画像保存パス');

            // プロフィール(任意：not null + '')
            // ※MySQLはバージョンにより TEXT に対して DEFAULT '' を付けられる環境と付けられない環境があります
            //   そのため string+文字数制限 とする
            $table->string('profile', 500)
                ->default('')
                ->after('icon_image_path')
                ->comment('プロフィール');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'icon_image_path',
                'profile',
            ]);
        });
    }
};
