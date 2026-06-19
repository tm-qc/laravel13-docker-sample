<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * rolesテーブルに管理用フラグを追加する
     *
     * is_system:
     * システム上必須のロールかどうかを判定する。
     * true のロールは、削除やis_active(非活性化)を禁止する想定。
     *
     * is_active:
     * ロールを現在使用可能かどうかを判定する。
     * 物理削除すると users.role_id とのリレーションが壊れる可能性があるため、
     * 削除の代わりに非活性として扱う。
     *
     * ※MySQLでは boolean は内部的には 1 のように見えることがあります。
     * 　Laravel側では true / false として扱えばOKです。
     *
     */
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table): void {
            /*
             * システム必須ロールフラグ
             *
             * true:
             * admin / general など、アプリの基本動作に必要なロール。
             * 削除や非活性化を禁止する。
             *
             * false:
             * 運用で追加した通常ロール。
             * 必要に応じて非活性化できる。
             *
             * 初期値は false にする。
             * 追加ロールまで自動でシステム必須扱いになるのを防ぐため。
             */
            $table->boolean('is_system')
                ->after('name')
                ->default(false)
                ->comment('システム必須ロールフラグ(非活性禁止(is_active = false))');

            /*
             * 有効フラグ
             *
             * true:
             * 現在使用できるロール。
             *
             * false:
             * 削除の代わりに使用停止したロール。
             *
             * roles は users.role_id から参照されるため、
             * 物理削除ではなく非活性化で扱う方が安全。
             *
             * 初期値は true にする。
             * 通常、作成直後のロールは使用可能にするため。
             */
            $table->boolean('is_active')
                ->after('is_system')
                ->default(true)
                ->comment('有効フラグ');
        });
    }

    /**
     * 追加した管理用フラグを削除する
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table): void {
            /*
             * rollback時は追加したカラムを削除する。
             *
             * is_system / is_active は外部キーではないため、
             * dropForeign は不要。
             */
            $table->dropColumn([
                'is_system',
                'is_active',
            ]);
        });
    }
};
