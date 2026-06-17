<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * php artisan migrate
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            /*
             * 論理削除用カラム
             *
             * null      : 削除されていない通常データ
             * 日時あり  : 論理削除済みデータ
             *
             * Laravel標準のSoftDeletesは、この deleted_at を見て
             * 通常の一覧取得から自動的に除外してくれる。
             *
             * SoftDeletes を追加した後は、$user->delete();が論理削除になる
             * 物理削除したい場合は$user->forceDelete();を使う
             */
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            /*
             * softDeletes() で追加した deleted_at カラムを削除する。
             */
            $table->dropSoftDeletes();
        });
    }
};
