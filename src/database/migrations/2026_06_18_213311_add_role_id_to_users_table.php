<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * usersテーブルにrole_idを追加する
     *
     * roles は親テーブル。
     * users は子テーブル。
     *
     * 1人のユーザーは1つのロールを持つ想定なので、
     * users.role_id で roles.id を参照する。
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            /*
             * ロールID
             *
             * users.role_id から roles.id を参照する。
             *
             * nullable() を付けないため NOT NULL になる。
             * 今回はユーザーに必ずロールを持たせる設計。
             *
             * restrictOnDelete():
             * このロールを使っているユーザーが存在する場合、
             * roles の削除をDB側で禁止する。
             *
             * 注意:
             * comment() などのカラム修飾は constrained() より前に書く。
             */
            $table->foreignId('role_id')
                ->after('id')
                ->comment('ロールID')
                ->constrained('roles')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            /*
             * 外部キー制約があるカラムを削除する場合は、
             * 先に外部キー制約を削除してからカラムを削除する。
             */
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};
