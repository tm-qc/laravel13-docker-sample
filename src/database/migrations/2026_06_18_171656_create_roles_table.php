<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * ロールテーブルを作成する
     *
     * id
     * code
     * name
     * created_at
     * updated_at
     *
     * roles は users から参照される親テーブル
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            // 主キー：子のusers.role_id から参照される
            $table->id();

            /*
             * ロールコード
             *
             * プログラム側で判定に使う固定値
             * 日本語ではなく admin / general のような英語コードで管理
             */
            $table->string('code', 50)
                ->unique()
                ->comment('ロールコード');

            /*
             * ロール名
             *
             * 画面表示用の名前
             *
             * admin   => 管理者
             * general => 一般
             */
            $table->string('name', 50)
                ->comment('ロール名');

            $table->timestamps();
        });
    }

    /**
     * ロールテーブルを削除する
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
