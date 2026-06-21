<?php

namespace App\Policies;

use App\Models\User;

/**
 * ポリシーメモ
 *
 * ポリシーはここで制限つけても各所に適用が必要になるため、
 * ロール追加 → 制御追加はコード改修ベースで行うことになりそう
*/
class UserPolicy
{
    /**
     * ユーザー一覧を表示できるか判定する
     *
     * 管理者だけ許可する。
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * ユーザー詳細を表示できるか判定する
     *
     * 管理者:
     * - 全ユーザーを表示できる
     *
     * 一般:
     * - 自分自身のみ表示できる
     */
    public function view(User $user, User $targetUser): bool
    {
        return $user->isAdmin()
            || $user->id === $targetUser->id;
    }


    /**
     * ユーザーを新規登録できるか判定する
     *
     * 管理者:
     * - ユーザー登録できる
     *
     * 一般:
     * - ユーザー登録できない
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * ユーザーを更新できるか判定する
     *
     * 管理者:
     * - 全ユーザーを更新できる
     *
     * 一般:
     * - 自分自身のみ更新できる
     */
    public function update(User $user, User $targetUser): bool
    {
        return $user->isAdmin()
            || $user->id === $targetUser->id;
    }

    /**
     * ユーザーを削除できるか判定する
     * ※soft delete実装済みの場合は論理削除
     *
     * 管理者:
     * - ユーザー削除できる
     *
     * 一般:
     * - ユーザー削除できない
     */
    public function delete(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * ユーザーを物理削除できるか判定する
     *
     * 物理削除は管理者だけ許可する。
     *
     */
    public function forceDelete(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * 論理削除済みユーザーを復元できるか判定する
     *
     * 復元は管理者だけ許可する
     */
    // public function restore(User $user, User $targetUser): bool
    // {
    //     return $user->isAdmin();
    // }

    /**
     * ユーザーのロールを変更できるか判定する
     *
     * 管理者のみ変更可能
     */
    public function changeRole(User $user): bool
    {
        return $user->isAdmin();
    }
}
